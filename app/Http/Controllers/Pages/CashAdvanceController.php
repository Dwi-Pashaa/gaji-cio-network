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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        $banks = XenditDisbursementService::availableBanks();
        $user  = Auth::user();

        return view("pages.cash-advance.index", compact("cashAdvance", "banks", "user"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title'               => 'required|string|max:255',
            'amount'              => 'required',
            'bank_name'           => 'required|string',
            'account_number'      => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:100',
        ]);

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

        // Simpan data rekening ke profil user jika belum ada
        if (!$user->bank_name || !$user->account_number) {
            $user->update([
                'bank_name'           => $request->bank_name,
                'account_number'      => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
            ]);
        }

        CashAdvance::create([
            'user_id'              => $user->id,
            'request_date'         => $requestDate,
            'amount'               => $amount,
            'title'                => $request->title,
            'bank_name'            => $request->bank_name,
            'account_number'       => $request->account_number,
            'account_holder_name'  => $request->account_holder_name,
        ]);

        $message = "Pengajuan Kasbon Baru\n\n"
            . "Nama    : {$user->name}\n"
            . "Judul   : {$request->title}\n"
            . "Jumlah  : Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Bank    : {$request->bank_name}\n"
            . "Rek     : {$request->account_number} a/n {$request->account_holder_name}\n"
            . "Tanggal : " . $requestDate->translatedFormat('l, d F Y');

        // Kirim WhatsApp Otomatis ke Admin via Mekari Qontak jika terkonfigurasi
        $qontak = app(MekariQontakService::class);
        if ($qontak->isConfigured()) {
            $qontak->notifyAdminNewCashAdvance(
                employeeName:  $user->name,
                title:         $request->title,
                amount:        (float) $amount,
                bankName:      $request->bank_name,
                accountNumber: $request->account_number,
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
        $validation = Validator::make($request->all(), [
            'title'               => 'required|string|max:255',
            'amount'              => 'required',
            'bank_name'           => 'required|string',
            'account_number'      => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:100',
        ]);

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

        $cashAdvance->update([
            'title'               => $request->title,
            'amount'              => $amountBaru,
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
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

        return view("pages.cash-advance.approval", compact("cashAdvance", "phone"));
    }

    public function approve(string $id)
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

        $financeApi = app(FinanceApiService::class);
        $xendit     = app(XenditDisbursementService::class);
        $user       = $cashAdvance->user ?? User::find($cashAdvance->user_id);
        $amount     = (float) $cashAdvance->amount;

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 1: Cek saldo website dari API Finance
        // ─────────────────────────────────────────────────────────────────
        if ($financeApi->isConfigured()) {
            $balanceResult = $financeApi->getBalance();

            if (!$balanceResult['success']) {
                return response()->json([
                    'code'   => 400,
                    'status' => 'error',
                    'message' => 'Gagal memeriksa saldo website: ' . $balanceResult['message'],
                ]);
            }

            if ($balanceResult['balance'] < $amount) {
                return response()->json([
                    'code'   => 400,
                    'status' => 'error',
                    'message' => 'Saldo Website Anda Tidak Cukup. '
                        . 'Saldo saat ini: Rp ' . number_format($balanceResult['balance'], 0, ',', '.') . ', '
                        . 'dibutuhkan: Rp ' . number_format($amount, 0, ',', '.') . '.',
                ]);
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 2: Set status transferring
        // ─────────────────────────────────────────────────────────────────
        $cashAdvance->update([
            'status'        => 'transferring',
            'approved_date' => Carbon::now(),
        ]);

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 3: Kirim disbursement via Xendit
        // ─────────────────────────────────────────────────────────────────
        if ($xendit->isConfigured()) {
            $disbResult = $xendit->sendDisbursement(
                externalId:         'KB-' . $cashAdvance->id . '-' . time(),
                bankCode:           $cashAdvance->bank_name,
                accountNumber:      $cashAdvance->account_number,
                accountHolderName:  $cashAdvance->account_holder_name,
                amount:             $amount,
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

            // Xendit sukses → update ke transferred
            $cashAdvance->update([
                'status'                 => 'transferred',
                'xendit_disbursement_id' => $disbResult['disbursement_id'],
                'xendit_status'          => $disbResult['status'],
                'transfer_at'            => Carbon::now(),
            ]);
        } else {
            // Xendit belum dikonfigurasi → langsung approved (manual transfer)
            $cashAdvance->update(['status' => 'approved']);
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 4: Potong saldo website langsung via Finance API
        // ─────────────────────────────────────────────────────────────────
        if ($financeApi->isConfigured()) {
            $financeApi->deductBalance(
                amount:       $amount,
                referenceId:  'KB-' . $cashAdvance->id,
                description:  'Pembayaran kasbon karyawan: ' . ($user->name ?? 'Karyawan'),
                category:     'kasbon',
                note:         'Kasbon: ' . $cashAdvance->title . ' - ' . ($user->name ?? '')
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

        // Hanya kirim WA langsung jika BUKAN via Xendit (manual transfer)
        // Jika via Xendit, WA akan dikirim dari XenditCallbackController saat COMPLETED
        if (!$xendit->isConfigured()) {
            if ($qontak->isConfigured() && $phoneUser) {
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
            ? 'Transfer kasbon Rp ' . number_format($amount, 0, ',', '.') . ' sedang diproses. Notifikasi WhatsApp akan dikirim ke karyawan setelah transfer dikonfirmasi.'
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

        $qontak       = app(MekariQontakService::class);
        $companySetting = Companie::latest()->first();
        $defaultPhone = MekariQontakService::formatPhone($companySetting?->telp) ?? '6285324780031';
        $phoneUser    = $user->phone ?? null;
        if ($phoneUser) {
            $phoneUser = MekariQontakService::formatPhone($phoneUser);
        }

        if ($qontak->isConfigured() && $phoneUser) {
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
