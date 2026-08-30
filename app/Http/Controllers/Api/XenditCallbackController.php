<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditCallbackController extends Controller
{
    /**
     * Webhook Callback dari Xendit untuk status Disbursement.
     * Berfungsi sebagai Router Central untuk multi-website.
     * POST /api/xendit/disbursement-callback
     */
    public function handleDisbursement(Request $request)
    {
        $payload        = $request->all();
        $disbursementId = $payload['id'] ?? null;
        $externalId     = $payload['external_id'] ?? null;
        $status         = strtoupper($payload['status'] ?? '');

        Log::info('[Xendit Webhook] Received disbursement callback', [
            'id'          => $disbursementId,
            'external_id' => $externalId,
            'status'      => $status,
        ]);

        if (!$disbursementId && !$externalId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // ─────────────────────────────────────────────────────────────────
        // 1. CEK APAKAH INI TRANSAKSI WEBSITE LAIN (FORWARDING)
        // ─────────────────────────────────────────────────────────────────
        $forwarders = config('services.xendit.forwarders', []);
        foreach ($forwarders as $target) {
            $prefix = $target['prefix'] ?? '';
            $url    = $target['url'] ?? '';

            if (!empty($prefix) && !empty($url) && $externalId && str_starts_with($externalId, $prefix)) {
                Log::info("[Xendit Webhook Router] Forwarding to external website [{$prefix}] -> {$url}");

                try {
                    $forwardHeaders = ['Content-Type' => 'application/json'];
                    if ($token = $request->header('x-callback-token')) {
                        $forwardHeaders['x-callback-token'] = $token;
                    }

                    $fwdResponse = Http::withHeaders($forwardHeaders)
                        ->timeout(15)
                        ->post($url, $payload);

                    Log::info("[Xendit Webhook Router] Forwarded successfully [{$prefix}]", [
                        'target_url'  => $url,
                        'status_code' => $fwdResponse->status(),
                    ]);

                    return response()->json([
                        'status'           => 'forwarded',
                        'forward_target'   => $prefix,
                        'forward_response' => $fwdResponse->json() ?? $fwdResponse->body(),
                    ], 200);
                } catch (\Exception $e) {
                    Log::error("[Xendit Webhook Router] Failed to forward [{$prefix}] -> {$url}: " . $e->getMessage());

                    return response()->json([
                        'status'  => 'forward_error',
                        'message' => 'Failed to forward to target website: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // 2. CEK APAKAH INI TRANSAKSI GAJI (prefix: GAJI-)
        //    external_id format: GAJI-{salary_id}-{user_id}-{timestamp}
        // ─────────────────────────────────────────────────────────────────
        if ($externalId && str_starts_with($externalId, 'GAJI-')) {
            if (preg_match('/^GAJI-(\d+)-(\d+)-/', $externalId, $matches)) {
                $salaryId = $matches[1];
                $salary   = \App\Models\Salary::with(['user', 'user.allowance'])->find($salaryId);

                if ($salary) {
                    Log::info('[Xendit Webhook] Processing salary transfer callback', [
                        'salary_id'   => $salaryId,
                        'external_id' => $externalId,
                        'status'      => $status,
                    ]);

                    if ($status === 'COMPLETED') {
                        Log::info('[Xendit Webhook] Salary transfer COMPLETED — sending WA notification', [
                            'salary_id' => $salaryId,
                            'employee'  => $salary->user->name ?? '-',
                        ]);

                        // ── Update status salary_payment ──
                        \App\Models\SalaryPayment::where('xendit_external_id', $externalId)
                            ->orWhere('xendit_disbursement_id', $disbursementId)
                            ->update([
                                'status'                 => 'transferred',
                                'xendit_status'          => 'COMPLETED',
                                'xendit_disbursement_id' => $disbursementId,
                                'transfer_at'            => Carbon::now(),
                            ]);

                        // ── Hitung ulang angka gaji untuk pesan WA ──
                        $user             = $salary->user;
                        $baseSalary       = (float) $salary->base_salary;
                        $totalAllowance   = (float) ($user->allowance->sum('amount') ?? 0);
                        $now              = Carbon::now();

                        $totalCashAdvance = (float) \App\Models\CashAdvance::where('user_id', $user->id)
                            ->whereIn('status', ['approved', 'transferred'])
                            ->whereMonth('request_date', $now->month)
                            ->whereYear('request_date', $now->year)
                            ->sum('amount');

                        $netSalary        = $baseSalary + $totalAllowance - $totalCashAdvance;
                        $monthYearStr     = $now->translatedFormat('F Y');
                        $dateStr          = $now->translatedFormat('l, d F Y - H:i');

                        $bankName          = $user->bank_name ?? '-';
                        $accountNumber     = $user->account_number ?? '-';
                        $accountHolderName = $user->account_holder_name ?? $user->name ?? '-';
                        $phoneUser         = $user->phone ? \App\Services\MekariQontakService::formatPhone($user->phone) : null;

                        // ── Kirim WA ke karyawan ──
                        if ($phoneUser) {
                            $qontak = app(\App\Services\MekariQontakService::class);
                            if ($qontak->isConfigured()) {
                                $qontak->notifySalaryTransfer(
                                    employeeName:      $user->name,
                                    employeePhone:     $phoneUser,
                                    monthYearStr:      $monthYearStr,
                                    baseSalary:        $baseSalary,
                                    totalAllowance:    $totalAllowance,
                                    totalCashAdvance:  $totalCashAdvance,
                                    netSalary:         $netSalary,
                                    bankName:          $bankName,
                                    accountNumber:     $accountNumber,
                                    accountHolderName: $accountHolderName,
                                    dateStr:           $dateStr
                                );

                                Log::info('[Xendit Webhook] WA notification sent to employee after COMPLETED', [
                                    'employee' => $user->name,
                                    'phone'    => $phoneUser,
                                ]);
                            }
                        }

                    } elseif ($status === 'FAILED') {
                        $failureCode = $payload['failure_code'] ?? '-';

                        Log::warning('[Xendit Webhook] Salary transfer FAILED — akan refund saldo', [
                            'salary_id'    => $salaryId,
                            'external_id'  => $externalId,
                            'failure_code' => $failureCode,
                        ]);

                        \App\Models\SalaryPayment::where('xendit_external_id', $externalId)
                            ->orWhere('xendit_disbursement_id', $disbursementId)
                            ->update([
                                'status'        => 'failed',
                                'xendit_status' => 'FAILED',
                            ]);

                        // ── Kembalikan saldo website Finance ──
                        $financeApi = app(\App\Services\FinanceApiService::class);
                        if ($financeApi->isConfigured()) {
                            $salaryPaymentRecord = \App\Models\SalaryPayment::where('xendit_external_id', $externalId)->first();
                            $refundAmount = $salaryPaymentRecord ? (float) $salaryPaymentRecord->net_salary : 0;

                            if ($refundAmount > 0) {
                                $refundResult = $financeApi->refundBalance(
                                    amount:      $refundAmount,
                                    referenceId: $externalId,
                                    description: 'Refund gaji ' . ($salary->user->name ?? '') . ' — transfer gagal (' . $failureCode . ')',
                                    reason:      $failureCode
                                );

                                Log::info('[Xendit Webhook] Refund saldo gaji', [
                                    'salary_id'     => $salaryId,
                                    'amount'        => $refundAmount,
                                    'refund_success'=> $refundResult['success'],
                                    'refund_msg'    => $refundResult['message'],
                                ]);
                            }
                        }
                    }

                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Salary transfer callback acknowledged for Salary #' . $salaryId . ' — status: ' . $status,
                    ], 200);
                }
            }

            Log::warning('[Xendit Webhook] Salary record not found for GAJI external_id', [
                'external_id' => $externalId,
            ]);
            return response()->json(['message' => 'Salary record not found, acknowledged'], 200);
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. PROSES KASBON (prefix: KB-)
        //    external_id format: KB-{id}-{timestamp}
        // ─────────────────────────────────────────────────────────────────
        $cashAdvance = null;
        if ($disbursementId) {
            $cashAdvance = CashAdvance::where('xendit_disbursement_id', $disbursementId)->first();
        }

        if (!$cashAdvance && $externalId) {
            if (preg_match('/^KB-(\d+)/', $externalId, $matches)) {
                $cashAdvance = CashAdvance::with('user')->find($matches[1]);
            }
        }

        if (!$cashAdvance) {
            Log::warning('[Xendit Webhook] CashAdvance not found and no matching forwarder', ['payload' => $payload]);
            return response()->json(['message' => 'No local record or forwarder matched, acknowledged'], 200);
        }

        $updateData = [
            'xendit_status' => $status,
        ];

        if ($status === 'COMPLETED') {
            $updateData['status']      = 'transferred';
            $updateData['transfer_at'] = Carbon::now();
        } elseif ($status === 'FAILED') {
            $updateData['status'] = 'failed';
        }

        $cashAdvance->update($updateData);

        // ── Refund saldo website jika kasbon FAILED ──
        if ($status === 'FAILED') {
            $failureCode = $payload['failure_code'] ?? 'TRANSFER_FAILED';
            $financeApi  = app(\App\Services\FinanceApiService::class);

            if ($financeApi->isConfigured()) {
                $refundAmount = (float) $cashAdvance->amount;

                if ($refundAmount > 0) {
                    $refundResult = $financeApi->refundBalance(
                        amount:      $refundAmount,
                        referenceId: $externalId ?? ('KB-' . $cashAdvance->id),
                        description: 'Refund kasbon ' . ($cashAdvance->user->name ?? '') . ' — transfer gagal (' . $failureCode . ')',
                        reason:      $failureCode
                    );

                    Log::info('[Xendit Webhook] Refund saldo kasbon', [
                        'cash_advance_id' => $cashAdvance->id,
                        'amount'          => $refundAmount,
                        'refund_success'  => $refundResult['success'],
                        'refund_msg'      => $refundResult['message'],
                    ]);
                }
            }
        }

        // ── Kirim WA ke karyawan saat COMPLETED ──
        if ($status === 'COMPLETED') {
            $user      = $cashAdvance->user ?? \App\Models\User::find($cashAdvance->user_id);
            $phoneUser = $user?->phone ? \App\Services\MekariQontakService::formatPhone($user->phone) : null;

            if ($phoneUser) {
                $qontak = app(\App\Services\MekariQontakService::class);
                if ($qontak->isConfigured()) {
                    try {
                        $invoiceUrl = route('cash.advance.invoice', $cashAdvance->id);
                    } catch (\Exception $e) {
                        $invoiceUrl = url('/cash-advance/' . $cashAdvance->id . '/invoice');
                    }

                    $qontak->notifyEmployeeApproval(
                        employeeName:      $user->name,
                        employeePhone:     $phoneUser,
                        title:             $cashAdvance->title,
                        amount:            (float) $cashAdvance->amount,
                        bankName:          $cashAdvance->bank_name ?? '-',
                        accountNumber:     $cashAdvance->account_number ?? '-',
                        accountHolderName: $cashAdvance->account_holder_name ?? $user->name,
                        dateStr:           Carbon::now()->translatedFormat('d F Y - H:i'),
                        transferred:       true,
                        invoiceUrl:        $invoiceUrl
                    );

                    Log::info('[Xendit Webhook] WA notification sent to employee after kasbon COMPLETED', [
                        'cash_advance_id' => $cashAdvance->id,
                        'employee'        => $user->name,
                        'phone'           => $phoneUser,
                    ]);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Callback processed locally for CashAdvance #' . $cashAdvance->id,
        ], 200);
    }
}
