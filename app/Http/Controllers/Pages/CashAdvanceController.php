<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\CashAdvanceType;
use App\Models\Companie;
use App\Models\User;
use App\Services\FinanceApiService;
use App\Services\MekariQontakService;
use App\Services\XenditDisbursementService;
use App\Mail\CashAdvanceApprovedMail;
use App\Mail\CashAdvanceRejectedMail;
use App\Mail\CashAdvanceSubmittedMail;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CashAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? null;
        $end   = $request->end ?? null;
        $sort  = $request->sort ?? 10;

        $user = Auth::user()->id;

        $cashAdvance = CashAdvance::where('user_id', $user)
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('request_date', [$start, $end]);
            })
            ->when($start && !$end, function ($query) use ($start) {
                $query->whereDate('request_date', '>=', $start);
            })
            ->when(!$start && $end, function ($query) use ($end) {
                $query->whereDate('request_date', '<=', $end);
            })
            ->orderBy('id', 'DESC')
            ->paginate($sort);

        $banks          = XenditDisbursementService::availableBanks();
        $user           = Auth::user();
        $maxCashAdvance = (float) Setting::get('max_cash_advance_amount', 0);
        $adminFee       = (float) Setting::get('admin_fee_disbursement', 2500);

        return view("pages.cash-advance.index", compact("cashAdvance", "banks", "user", "maxCashAdvance", "adminFee"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $paymentType = $request->input('payment_type', 'xendit');

        $rules = [
            'title'        => 'required|string|max:255',
            'amount'       => 'required',
            'payment_type' => 'required|in:xendit,manual',
        ];

        if ($paymentType === 'xendit') {
            $rules['bank_name']           = 'required|string';
            $rules['account_number']      = 'required|string|max:50';
            $rules['account_holder_name'] = 'required|string|max:100';
        }

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'errors' => $validation->errors(),
            ]);
        }

        $user        = Auth::user();
        $requestDate = Carbon::now();
        $amount      = preg_replace('/[^0-9]/', '', $request->amount);
        $adminFee    = ($paymentType === 'xendit') ? (float) Setting::get('admin_fee_disbursement', 0) : 0.0;

        // Validasi batas maksimal nominal pengajuan kasbon
        $maxCashAdvance = (float) Setting::get('max_cash_advance_amount', 0);
        if ($maxCashAdvance > 0 && (float) $amount > $maxCashAdvance) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'errors' => [
                    'amount' => ['Nominal pengajuan kasbon maksimal yang diizinkan adalah Rp ' . number_format($maxCashAdvance, 0, ',', '.')]
                ],
                'message' => 'Nominal pengajuan kasbon (Rp ' . number_format((float) $amount, 0, ',', '.') . ') melebihi batas maksimal yang diizinkan (Rp ' . number_format($maxCashAdvance, 0, ',', '.') . ').',
            ]);
        }

        // Simpan data rekening ke profil user jika belum ada (hanya jika xendit)
        if ($paymentType === 'xendit' && (!$user->bank_name || !$user->account_number)) {
            $user->update([
                'bank_name'           => $request->bank_name,
                'account_number'      => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
            ]);
        }

        $cashAdvance = CashAdvance::create([
            'user_id'              => $user->id,
            'request_date'         => $requestDate,
            'amount'               => $amount,
            'payment_type'         => $paymentType,
            'admin_fee'            => $adminFee,
            'title'                => $request->title,
            'bank_name'            => ($paymentType === 'xendit') ? $request->bank_name : null,
            'account_number'       => ($paymentType === 'xendit') ? $request->account_number : null,
            'account_holder_name'  => ($paymentType === 'xendit') ? $request->account_holder_name : null,
        ]);

        $paymentLabel = ($paymentType === 'xendit') ? "Transfer Bank (Xendit)" : "Uang Tunai / Kas";
        $message = "Pengajuan Kasbon Baru\n\n"
            . "Nama    : {$user->name}\n"
            . "Judul   : {$request->title}\n"
            . "Jumlah  : Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Metode  : {$paymentLabel}\n"
            . ($paymentType === 'xendit' ? "Rek     : {$request->account_number} a/n {$request->account_holder_name} ({$request->bank_name})\n" : "")
            . "Tanggal : " . $requestDate->translatedFormat('l, d F Y');

        $companie = Companie::latest()->first();
        $telp = $companie?->telp;
        $waLink = null;
        if ($telp) {
            $waNumber = preg_replace('/^0/', '62', $telp);
            $waLink = "https://wa.me/{$waNumber}?text=" . rawurlencode($message);
        }

        // 1. Notifikasi Email
        if (Setting::get('notify_cash_advance_email', '1') == '1') {
            try {
                // Email ke Karyawan
                if (!empty($user->email)) {
                    Mail::to($user->email)->send(new CashAdvanceSubmittedMail($cashAdvance, $user, false));
                }

                // Email ke Admin
                $adminEmail = Setting::get('admin_notification_email', config('mail.from.address', 'support@cionetwork.id'));
                if (!empty($adminEmail)) {
                    Mail::to($adminEmail)->send(new CashAdvanceSubmittedMail($cashAdvance, $user, true));
                }
            } catch (\Throwable $th) {
                Log::warning('[CashAdvance Email] Gagal mengirim email pengajuan kasbon: ' . $th->getMessage());
            }
        }

        // 2. Notifikasi WhatsApp ke Admin via Mekari Qontak
        $qontak = app(MekariQontakService::class);
        if (Setting::get('notify_cash_advance_wa', '1') == '1' && $qontak->isConfigured()) {
            $qontak->notifyAdminNewCashAdvance(
                employeeName:  $user->name,
                title:         $request->title,
                amount:        (float) $amount,
                bankName:      ($paymentType === 'xendit') ? ($request->bank_name ?? '-') : 'Tunai / Kas',
                accountNumber: ($paymentType === 'xendit') ? ($request->account_number ?? '-') : '-',
                dateStr:       $requestDate->translatedFormat('d F Y')
            );
        }

        return response()->json([
            'code'    => 200,
            'status'  => 'success',
            'message' => 'Berhasil mengajukan kasbon.',
            'wa_link' => $qontak->isConfigured() ? null : $waLink,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        return response()->json(['code' => 200, 'status' => 'success', 'data' => $cashAdvance]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentType = $request->input('payment_type', 'xendit');

        $rules = [
            'title'        => 'required|string|max:255',
            'amount'       => 'required',
            'payment_type' => 'required|in:xendit,manual',
        ];

        if ($paymentType === 'xendit') {
            $rules['bank_name']           = 'required|string';
            $rules['account_number']      = 'required|string|max:50';
            $rules['account_holder_name'] = 'required|string|max:100';
        }

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'errors' => $validation->errors()
            ]);
        }

        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code'    => 404,
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.',
            ]);
        }

        if ($cashAdvance->status !== 'pending' && $cashAdvance->status !== 'failed') {
            return response()->json([
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Kasbon yang sudah diproses tidak dapat diubah.',
            ]);
        }

        $user       = Auth::user();
        $amountBaru = preg_replace('/[^0-9]/', '', $request->amount);
        $adminFee   = ($paymentType === 'xendit') ? (float) Setting::get('admin_fee_disbursement', 0) : 0.0;

        $cashAdvance->update([
            'title'               => $request->title,
            'amount'              => $amountBaru,
            'payment_type'        => $paymentType,
            'admin_fee'           => $adminFee,
            'bank_name'           => ($paymentType === 'xendit') ? $request->bank_name : null,
            'account_number'      => ($paymentType === 'xendit') ? $request->account_number : null,
            'account_holder_name' => ($paymentType === 'xendit') ? $request->account_holder_name : null,
            'updated_at'          => Carbon::now(),
        ]);

        $message = "Perubahan Pengajuan Kasbon\n\n"
            . "Nama    : {$user->name}\n"
            . "Judul   : {$request->title}\n"
            . "Jumlah  : Rp" . number_format($amountBaru, 0, ',', '.') . "\n"
            . "Bank    : {$request->bank_name}\n"
            . "Rek     : {$request->account_number} a/n {$request->account_holder_name}\n"
            . "Tanggal : " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $companie = Companie::latest()->first();
        $telp = $companie->telp;
        $waNumber = preg_replace('/^0/', '62', $telp);
        $waLink = "https://wa.me/{$waNumber}?text=" . rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil memperbarui data kasbon.',
            'wa_link' => $waLink
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        if ($cashAdvance->type_id) {
            $type = CashAdvanceType::find($cashAdvance->type_id);
            if ($type) {
                $type->amount += $cashAdvance->amount;
                $type->save();
            }
        }

        $cashAdvance->delete();

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menghapus data.']);
    }

    public function invoice(string $id)
    {
        $cashAdvance = CashAdvance::with('user')->find($id);

        if (!$cashAdvance) {
            abort(404, 'Data kasbon tidak ditemukan');
        }

        $user = Auth::user();
        // Hanya pemilik kasbon atau user berhak (admin) yang bisa akses
        if ($cashAdvance->user_id !== $user->id && !$user->can('approve kasbon') && !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses ke invoice ini');
        }

        $companie = Companie::latest()->first();

        return view('pages.cash-advance.invoice', compact('cashAdvance', 'companie'));
    }

    public function syncStatus(string $id, XenditDisbursementService $xendit)
    {
        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json(['code' => 404, 'status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        if (!$cashAdvance->xendit_disbursement_id) {
            return response()->json(['code' => 400, 'status' => 'error', 'message' => 'Kasbon ini tidak memiliki ID transfer Xendit.']);
        }

        $res = $xendit->getDisbursementById($cashAdvance->xendit_disbursement_id);

        if (!$res['success']) {
            return response()->json(['code' => 400, 'status' => 'error', 'message' => $res['message']]);
        }

        $xenditStatus = strtoupper($res['status'] ?? '');
        $updateData   = ['xendit_status' => $xenditStatus];

        if ($xenditStatus === 'COMPLETED') {
            $updateData['status']      = 'transferred';
            $updateData['transfer_at'] = Carbon::now();
        } elseif ($xenditStatus === 'FAILED') {
            $updateData['status'] = 'failed';
        }

        $cashAdvance->update($updateData);

        return response()->json([
            'code'          => 200,
            'status'        => 'success',
            'message'       => 'Status terkini: ' . $xenditStatus,
            'xendit_status' => $xenditStatus,
            'item_status'   => $cashAdvance->status,
        ]);
    }

    public function approval(Request $request)
    {
        $start = $request->start ?? null;
        $end   = $request->end ?? null;
        $sort  = $request->sort ?? 10;

        $cashAdvance = CashAdvance::with(['user', 'type'])
            ->when($start && $end, function ($query) use ($start, $end) {
                $query->whereBetween('request_date', [$start, $end]);
            })
            ->when($start && !$end, function ($query) use ($start) {
                $query->whereDate('request_date', '>=', $start);
            })
            ->when(!$start && $end, function ($query) use ($end) {
                $query->whereDate('request_date', '<=', $end);
            })
            ->orderBy('id', 'DESC')
            ->paginate($sort);

        $phone = Companie::latest()->first();
        $banks = app(XenditDisbursementService::class)->getSupportedBanks();

        return view("pages.cash-advance.approval", compact("cashAdvance", "phone", "banks"));
    }

    public function calculateTransfer(string $id)
    {
        $cashAdvance = CashAdvance::with('user')->find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code'    => 404,
                'status'  => false,
                'message' => 'Data kasbon tidak ditemukan.',
            ]);
        }

        $user           = $cashAdvance->user;
        $amount         = (float) $cashAdvance->amount;
        $paymentType    = $cashAdvance->payment_type ?: 'xendit';
        $adminFee       = ($paymentType === 'xendit') ? (float) ($cashAdvance->admin_fee > 0 ? $cashAdvance->admin_fee : Setting::get('admin_fee_disbursement', 2500)) : 0.0;
        $amountXendit   = max(0, $amount - $adminFee);
        $amountManual   = $amount;

        $financeApi     = app(FinanceApiService::class);
        $balRes         = $financeApi->getBalance();
        $balanceManual  = (is_array($balRes) && isset($balRes['balance_manual'])) ? (float) $balRes['balance_manual'] : 0.0;
        $balanceXendit  = (is_array($balRes) && isset($balRes['balance_xendit'])) ? (float) $balRes['balance_xendit'] : 0.0;
        $totalBalance   = (is_array($balRes) && isset($balRes['total_balance'])) ? (float) $balRes['total_balance'] : ($balanceManual + $balanceXendit);
        $balanceVal     = (is_array($balRes) && isset($balRes['balance'])) ? (float) $balRes['balance'] : $totalBalance;

        return response()->json([
            'code'   => 200,
            'status' => true,
            'data'   => [
                'cash_advance_id'        => $cashAdvance->id,
                'user_id'                => $user?->id,
                'user_name'              => $user?->name ?? 'Karyawan',
                'user_phone'             => $user?->phone,
                'title'                  => $cashAdvance->title,
                'payment_type'           => $paymentType,
                'request_date'           => Carbon::parse($cashAdvance->request_date)->translatedFormat('d F Y'),
                'amount'                 => $amount,
                'admin_fee'              => $adminFee,
                'amount_xendit'          => $amountXendit,
                'amount_manual'          => $amountManual,
                'bank_name'              => $cashAdvance->bank_name ?? $user?->bank_name,
                'account_number'         => $cashAdvance->account_number ?? $user?->account_number,
                'account_holder_name'    => $cashAdvance->account_holder_name ?? $user?->account_holder_name ?? $user?->name,
                'balance_manual'         => $balanceManual,
                'balance_xendit'         => $balanceXendit,
                'total_balance'          => $totalBalance,
                'channel_status'         => $balRes['channel_status'] ?? ['manual' => true, 'xendit' => true],
                'channel_manual_enabled' => $balRes['channel_manual_enabled'] ?? true,
                'channel_xendit_enabled' => $balRes['channel_xendit_enabled'] ?? true,
                'current_balance'        => $balanceVal,
            ],
        ]);
    }

    public function approve(Request $request, string $id)
    {
        $cashAdvance = CashAdvance::with('user')->find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message' => 'Data kasbon tidak ditemukan.',
            ]);
        }

        if (!in_array($cashAdvance->status, ['pending', 'failed'])) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message' => 'Kasbon ini sudah diproses sebelumnya (status: ' . $cashAdvance->statusLabel() . ').',
            ]);
        }

        $rawType        = $request->input('transfer_type') ?: ($cashAdvance->payment_type ?: 'xendit');
        $transferType   = in_array($rawType, ['xendit', 'manual']) ? $rawType : 'xendit';
        $financeApi     = app(FinanceApiService::class);
        $xendit         = app(XenditDisbursementService::class);
        $user           = $cashAdvance->user ?? User::find($cashAdvance->user_id);
        $amount         = (float) $cashAdvance->amount;
        $adminFee       = ($transferType === 'xendit') ? (float) ($cashAdvance->admin_fee > 0 ? $cashAdvance->admin_fee : Setting::get('admin_fee_disbursement', 2500)) : 0.0;
        $transferAmount = max(0, $amount - $adminFee);

        if ($transferAmount <= 0) {
            return response()->json([
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Nominal transfer kasbon Rp 0 atau minus setelah dipotong biaya admin.',
            ]);
        }

        // Update rekening jika transfer via Xendit
        $bankName          = $request->input('bank_name', $cashAdvance->bank_name);
        $accountNumber     = $request->input('account_number', $cashAdvance->account_number);
        $accountHolderName = $request->input('account_holder_name', $cashAdvance->account_holder_name ?? $user?->name);

        if ($transferType === 'xendit' && $bankName && $accountNumber) {
            $cashAdvance->update([
                'bank_name'           => $bankName,
                'account_number'      => $accountNumber,
                'account_holder_name' => $accountHolderName,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 1: Cek saldo website dari API Finance & Channel Status
        // ─────────────────────────────────────────────────────────────────
        if ($financeApi->isConfigured()) {
            $balanceResult = $financeApi->getBalance();

            if (!$balanceResult['success']) {
                return response()->json([
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'Gagal memeriksa saldo website: ' . $balanceResult['message'],
                ]);
            }

            $balManual     = (float) ($balanceResult['balance_manual'] ?? 0);
            $balXendit     = (float) ($balanceResult['balance_xendit'] ?? 0);
            $manualEnabled = (bool) ($balanceResult['channel_manual_enabled'] ?? true);
            $xenditEnabled = (bool) ($balanceResult['channel_xendit_enabled'] ?? true);

            if ($transferType === 'manual') {
                if (!$manualEnabled) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => 'error',
                        'message' => 'Saluran Saldo Manual sedang dinonaktifkan oleh administrator Finance API.',
                    ]);
                }
                if ($balManual < $transferAmount) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => 'error',
                        'message' => 'Saldo Manual Tidak Cukup. Saldo saat ini: Rp ' . number_format($balManual, 0, ',', '.') . ', dibutuhkan: Rp ' . number_format($transferAmount, 0, ',', '.') . '.',
                    ]);
                }
            } else {
                if (!$xenditEnabled) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => 'error',
                        'message' => 'Saluran Saldo Xendit sedang dinonaktifkan oleh administrator Finance API.',
                    ]);
                }
                if ($balXendit < $transferAmount) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => 'error',
                        'message' => 'Saldo Xendit Tidak Cukup. Saldo saat ini: Rp ' . number_format($balXendit, 0, ',', '.') . ', dibutuhkan: Rp ' . number_format($transferAmount, 0, ',', '.') . '.',
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 2: Set status transferring & payment_type
        // ─────────────────────────────────────────────────────────────────
        $cashAdvance->update([
            'payment_type'  => $transferType,
            'admin_fee'     => $adminFee,
            'status'        => ($transferType === 'xendit' && $xendit->isConfigured()) ? 'transferring' : 'transferred',
            'approved_date' => Carbon::now(),
            'transfer_at'   => ($transferType === 'xendit' && $xendit->isConfigured()) ? null : Carbon::now(),
        ]);

        $externalId = 'KB-' . $cashAdvance->id . '-' . time();

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 3: Kirim disbursement via Xendit (Jika tipe Xendit)
        // ─────────────────────────────────────────────────────────────────
        if ($transferType === 'xendit' && $xendit->isConfigured()) {
            $disbResult = $xendit->sendDisbursement(
                externalId:         $externalId,
                bankCode:           $cashAdvance->bank_name,
                accountNumber:      $cashAdvance->account_number,
                accountHolderName:  $cashAdvance->account_holder_name,
                amount:             $transferAmount,
                description:        'Kasbon: ' . $cashAdvance->title . ' - ' . ($user->name ?? 'Karyawan')
            );

            if (!$disbResult['success']) {
                // Xendit gagal → rollback status ke failed
                $cashAdvance->update([
                    'status'       => 'failed',
                    'xendit_status'=> 'FAILED',
                ]);

                Log::warning('[CashAdvance] Xendit disbursement failed', [
                    'cash_advance_id' => $cashAdvance->id,
                    'xendit_message'  => $disbResult['message'],
                ]);

                return response()->json([
                    'code'   => 400,
                    'status' => 'error',
                    'message' => 'Gagal melakukan transfer via Xendit: ' . $disbResult['message'],
                ]);
            }

            // Xendit sukses dikirim
            $cashAdvance->update([
                'xendit_disbursement_id' => $disbResult['disbursement_id'],
                'xendit_status'          => $disbResult['status'] ?? 'PENDING',
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 4: Potong saldo website langsung via Finance API
        // ─────────────────────────────────────────────────────────────────
        if ($financeApi->isConfigured()) {
            $financeApi->deductBalance(
                amount:       $transferAmount,
                referenceId:  $externalId,
                description:  'Pembayaran kasbon karyawan: ' . ($user->name ?? 'Karyawan') . ($transferType === 'xendit' ? ($adminFee > 0 ? ' (terpotong admin Rp ' . number_format($adminFee, 0, ',', '.') . ')' : '') : ' (Manual)'),
                category:     'kasbon',
                note:         'Pengajuan Kasbon: Rp ' . number_format($amount, 0, ',', '.') . ($adminFee > 0 ? ' - Potongan Admin: Rp ' . number_format($adminFee, 0, ',', '.') : '') . ' = Ditransfer: Rp ' . number_format($transferAmount, 0, ',', '.'),
                balanceType:  $transferType
            );
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 5: Notif WA
        // - Jika via Xendit: WA dikirim nanti via webhook COMPLETED
        // - Jika manual (Xendit tidak dikonfigurasi): kirim WA sekarang
        // ─────────────────────────────────────────────────────────────────
        $qontak         = app(MekariQontakService::class);
        $companySetting = Companie::latest()->first();
        $defaultPhone   = MekariQontakService::formatPhone($companySetting?->telp) ?? '6285324780031';
        $phoneUser      = $user->phone ?? null;
        if ($phoneUser) {
            $phoneUser = MekariQontakService::formatPhone($phoneUser);
        }

        $transferred = ($cashAdvance->status === 'transferred');
        $invoiceUrl  = route('cash.advance.invoice', $cashAdvance->id);

        // Hanya kirim notifikasi langsung jika BUKAN via Xendit (manual transfer)
        // Jika via Xendit, notifikasi akan dikirim dari XenditCallbackController saat COMPLETED
        if (!$xendit->isConfigured()) {
            // Notifikasi Email ke Karyawan
            if (Setting::get('notify_cash_advance_email', '1') == '1' && !empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new CashAdvanceApprovedMail($cashAdvance, $user, $transferred, $invoiceUrl));
                } catch (\Throwable $th) {
                    Log::warning('[CashAdvance Email] Gagal mengirim email persetujuan kasbon: ' . $th->getMessage());
                }
            }

            // Notifikasi WhatsApp ke Karyawan
            if (Setting::get('notify_cash_advance_wa', '1') == '1' && $qontak->isConfigured() && $phoneUser) {
                $qontak->notifyEmployeeApproval(
                    employeeName:       $user->name,
                    employeePhone:      $phoneUser,
                    title:              $cashAdvance->title,
                    amount:             $amount,
                    bankName:           $cashAdvance->bank_name ?? '-',
                    accountNumber:      $cashAdvance->account_number ?? '-',
                    accountHolderName:  $cashAdvance->account_holder_name ?? $user->name,
                    dateStr:            Carbon::now()->translatedFormat('d F Y - H:i'),
                    transferred:        $transferred,
                    invoiceUrl:         $invoiceUrl
                );
            }
        }

        $message = "Konfirmasi Kasbon " . ($transferred ? 'Disetujui & Ditransfer' : 'Disetujui') . "\n\n"
            . "Nama    : {$user->name}\n"
            . "Judul   : {$cashAdvance->title}\n"
            . "Jumlah  : Rp" . number_format($amount, 0, ',', '.') . "\n"
            . ($transferred
                ? "Bank    : {$cashAdvance->bank_name}\nRek     : {$cashAdvance->account_number} a/n {$cashAdvance->account_holder_name}\n"
                : '')
            . "Tanggal : " . Carbon::now()->translatedFormat('l, d F Y - H:i') . "\n\n"
            . "Bukti Transfer / Invoice:\n" . $invoiceUrl;

        $encodedMsg = rawurlencode($message);
        $successMsg = $xendit->isConfigured()
            ? 'Transfer kasbon Rp ' . number_format($amount, 0, ',', '.') . ' sedang diproses. Notifikasi akan dikirim ke karyawan setelah transfer dikonfirmasi.'
            : ($transferred
                ? 'Kasbon Rp ' . number_format($amount, 0, ',', '.') . ' berhasil disetujui & ditransfer ke ' . $cashAdvance->account_holder_name . '.'
                : 'Kasbon berhasil disetujui.');

        return response()->json([
            'code'            => 200,
            'status'          => 'success',
            'message'         => $successMsg,
            'wa_link_user'    => ($qontak->isConfigured() || $xendit->isConfigured()) ? null : ($phoneUser ? "https://wa.me/{$phoneUser}?text={$encodedMsg}" : null),
            'wa_link_default' => ($qontak->isConfigured() || $xendit->isConfigured()) ? null : "https://wa.me/{$defaultPhone}?text={$encodedMsg}",
        ]);
    }

    public function rejected(string $id)
    {
        $cashAdvance = CashAdvance::with('user')->find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message' => 'Data tidak ditemukan.',
            ]);
        }

        $user = $cashAdvance->user ?? User::find($cashAdvance->user_id);

        $cashAdvance->update([
            'status'        => 'rejected',
            'approved_date' => Carbon::now(),
        ]);

        // Notifikasi Email ke Karyawan
        if (Setting::get('notify_cash_advance_email', '1') == '1' && !empty($user->email)) {
            try {
                Mail::to($user->email)->send(new CashAdvanceRejectedMail($cashAdvance, $user));
            } catch (\Throwable $th) {
                Log::warning('[CashAdvance Email] Gagal mengirim email penolakan kasbon: ' . $th->getMessage());
            }
        }

        $qontak       = app(MekariQontakService::class);
        $companySetting = Companie::latest()->first();
        $defaultPhone = MekariQontakService::formatPhone($companySetting?->telp) ?? '6285324780031';
        $phoneUser    = $user->phone ?? null;
        if ($phoneUser) {
            $phoneUser = MekariQontakService::formatPhone($phoneUser);
        }

        if (Setting::get('notify_cash_advance_wa', '1') == '1' && $qontak->isConfigured() && $phoneUser) {
            $qontak->notifyEmployeeRejection(
                employeeName:  $user->name,
                employeePhone: $phoneUser,
                title:         $cashAdvance->title,
                amount:        (float) $cashAdvance->amount,
                dateStr:       Carbon::now()->translatedFormat('d F Y - H:i')
            );
        }

        $message = "Konfirmasi Kasbon Ditolak\n\n"
            . "Nama    : {$user->name}\n"
            . "Judul   : {$cashAdvance->title}\n"
            . "Jumlah  : Rp" . number_format($cashAdvance->amount, 0, ',', '.') . "\n"
            . "Tanggal : " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $encodedMsg = rawurlencode($message);

        return response()->json([
            'code'            => 200,
            'status'          => 'success',
            'message'         => 'Kasbon berhasil ditolak.',
            'wa_link_user'    => $qontak->isConfigured() ? null : ($phoneUser ? "https://wa.me/{$phoneUser}?text={$encodedMsg}" : null),
            'wa_link_default' => $qontak->isConfigured() ? null : "https://wa.me/{$defaultPhone}?text={$encodedMsg}",
        ]);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            "phone" => "required"
        ]);

        $companie = Companie::first();

        if ($companie) {
            $companie->update([
                "telp" => $request->phone
            ]);
        } else {
            $companie = Companie::create([
                "telp" => $request->phone
            ]);
        }

        return back()->with('success', 'Berhasil menyimpan data.');
    }
}
