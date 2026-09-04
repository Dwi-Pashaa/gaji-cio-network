<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\CashAdvance;
use App\Models\Salary;
use App\Models\User;
use App\Models\Companie;
use App\Models\SalaryPayment;
use App\Models\UserAllownce;
use App\Mail\SalaryTransferredMail;
use App\Models\Setting;
use App\Services\FinanceApiService;
use App\Services\MekariQontakService;
use App\Services\XenditDisbursementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $sort   = $request->sort ?? null;

        $user = User::all();
        $allowance = Allowance::all();

        $salary = Salary::with(['user', 'user.allowance', 'payments'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->paginate($sort);

        $banks = XenditDisbursementService::getSupportedBanks();
        $roles = Role::all();

        return view("pages.salarie.index", compact("user", "allowance", "salary", "banks", "roles"));
    }

    /**
     * Riwayat pembayaran gaji karyawan
     */
    public function paymentHistory(Request $request)
    {
        $query = SalaryPayment::with(['user', 'transferredBy'])
            ->orderBy('created_at', 'DESC');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('month')) {
            $query->where('period_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('period_year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(25)->withQueryString();

        $totalTransferred = SalaryPayment::where('status', 'transferred')->sum('net_salary');
        $totalPending     = SalaryPayment::where('status', 'pending')->count();
        $totalFailed      = SalaryPayment::where('status', 'failed')->count();

        return view('pages.salarie.payment_history', compact(
            'payments', 'totalTransferred', 'totalPending', 'totalFailed'
        ));
    }


    public function store(Request $request)
    {
        $employeeMode = $request->input('employee_mode', 'new'); // 'new' or 'existing'

        $rules = [
            "base_salary"    => "required|string",
            "effective_date" => "required|date",
            "status"         => "required|string",
            "allowance_id"   => "array|nullable",
        ];

        if ($employeeMode === 'existing') {
            $rules['user_id'] = 'required|exists:users,id';
        } else {
            $rules['name']                  = 'required|string|max:255';
            $rules['username']              = 'required|string|max:255|unique:users,username';
            $rules['email']                 = 'required|email|max:255|unique:users,email';
            $rules['phone']                 = 'required|string|max:30';
            $rules['password']              = 'required|string|min:6';
            $rules['role']                  = 'nullable|string';
            $rules['bank_name']             = 'nullable|string';
            $rules['account_number']        = 'nullable|string';
            $rules['account_holder_name']   = 'nullable|string';
        }

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'code'   => 400,
                'status' => false,
                'errors' => $validation->errors()
            ]);
        }

        DB::beginTransaction();
        try {
            if ($employeeMode === 'existing') {
                $userId = $request->user_id;
            } else {
                $user = User::create([
                    'name'                => $request->name,
                    'username'            => $request->username,
                    'email'               => $request->email,
                    'phone'               => $request->phone,
                    'password'            => Hash::make($request->password),
                    'bank_name'           => $request->bank_name,
                    'account_number'      => $request->account_number,
                    'account_holder_name' => $request->account_holder_name ?: $request->name,
                ]);

                $roleName = $request->role ?: 'Karyawan';
                $user->assignRole($roleName);
                $userId = $user->id;
            }

            $base_salary = preg_replace('/[^0-9]/', '', $request->base_salary);

            $existingSalary = Salary::where('user_id', $userId)->first();
            if ($existingSalary) {
                $existingSalary->update([
                    'base_salary'    => $base_salary,
                    'effective_date' => $request->effective_date,
                    'status'         => $request->status,
                ]);
            } else {
                Salary::create([
                    'user_id'        => $userId,
                    'base_salary'    => $base_salary,
                    'effective_date' => $request->effective_date,
                    'status'         => $request->status,
                ]);
            }

            UserAllownce::where('user_id', $userId)->delete();
            foreach ((array) $request->allowance_id as $allowanceId) {
                if (!empty($allowanceId)) {
                    UserAllownce::create([
                        'user_id'      => $userId,
                        'allowance_id' => $allowanceId,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'code'    => 200,
                'status'  => true,
                'message' => 'Berhasil membuat karyawan dan konfigurasi gaji.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('[Salary Store] Error: ' . $th->getMessage());
            return response()->json([
                'code'    => 500,
                'status'  => false,
                'message' => 'Gagal menyimpan data: ' . $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $salary = Salary::with(['user', 'user.allowance'])->find($id);

        if (!$salary) {
            return response()->json(['code' => 400, 'status' => false, 'message' => 'data not found.']);
        }

        return response()->json(['code' => 200, 'status' => true, 'data' => $salary]);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            "user_id"             => "required|integer",
            "base_salary"         => "required|string",
            "effective_date"      => "required|date",
            "status"              => "required|string",
            "allowance_id"        => "array|nullable",
            "bank_name"           => "nullable|string",
            "account_number"      => "nullable|string",
            "account_holder_name" => "nullable|string",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code'   => 400,
                'status' => false,
                'errors' => $validation->errors()
            ]);
        }

        $salary = Salary::findOrFail($id);
        $base_salary = preg_replace('/[^0-9]/', '', $request->base_salary);

        $salary->update([
            'user_id'        => $request->user_id,
            'base_salary'    => $base_salary,
            'effective_date' => $request->effective_date,
            'status'         => $request->status,
        ]);

        // Update bank info user jika ada
        if ($salary->user) {
            $userUpdate = [];
            if ($request->filled('bank_name')) $userUpdate['bank_name'] = $request->bank_name;
            if ($request->filled('account_number')) $userUpdate['account_number'] = $request->account_number;
            if ($request->filled('account_holder_name')) $userUpdate['account_holder_name'] = $request->account_holder_name;
            if (!empty($userUpdate)) {
                $salary->user->update($userUpdate);
            }
        }

        UserAllownce::where('user_id', $request->user_id)->delete();

        foreach ((array) $request->allowance_id as $allowanceId) {
            if ($allowanceId) {
                UserAllownce::create([
                    'user_id'      => $request->user_id,
                    'allowance_id' => $allowanceId,
                ]);
            }
        }

        return response()->json([
            'code'    => 200,
            'status'  => true,
            'message' => 'Berhasil mengupdate data gaji pegawai.'
        ]);
    }

    public function destroy(string $id)
    {
        $salary = Salary::find($id);

        if (!$salary) {
            return response()->json(['code' => 400, 'status' => false, 'message' => 'data not found.']);
        }

        UserAllownce::where('user_id', $salary->id)->delete();

        $salary->delete();

        return response()->json(['code' => 200, 'status' => true, 'message' => 'Berhasil menghapus data']);
    }

    public function recap(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year  = $request->year ?? Carbon::now()->year;

        $salaryHistories = collect();

        $users = User::with(['salary', 'allowance'])
            ->whereHas('salary', function ($q) use ($month, $year) {
                $q->whereMonth('effective_date', '<=', $month)
                    ->whereYear('effective_date', '<=', $year);
            })
            ->get();

        foreach ($users as $user) {
            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount');

            $cashAdvance = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $salaryHistories->push([
                'user_id'      => $user->id,
                'name'         => $user->name,
                'year'         => $year,
                'month'        => $month,
                'base_salary'  => $baseSalary,
                'allowance'    => $totalAllowance,
                'cash_advance' => $cashAdvance,
                'net_salary'   => $baseSalary + $totalAllowance - $cashAdvance,
            ]);
        }

        // dd($salaryHistories);

        return view("pages.salarie.recap", compact("salaryHistories", "month", "year"));
    }

    public function detail($month, $year, $id)
    {
        $user = User::find($id);

        $baseSalary     = $user->salary->base_salary ?? 0;
        $allowances     = $user->allowance;
        $totalAllowance = $allowances->sum('amount');

        $cashAdvance = CashAdvance::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        $cashAdvanceTotal = $cashAdvance->sum('amount');

        $netSalary = $baseSalary + $totalAllowance - $cashAdvanceTotal;

        $data = [
            'user'          => $user,
            'year'          => $year,
            'month'         => $month,
            'base_salary'    => $baseSalary,
            'allowances'    => $allowances,
            'total_allowance' => $totalAllowance,
            'cash_advances'   => $cashAdvance,
            'net_salary'     => $netSalary,
            'total_cash_advance' => $cashAdvanceTotal
        ];

        return view("pages.salarie.detail", compact("data"));
    }

    /**
     * Mengambil rincian kalkulasi transfer gaji (Gaji Pokok + Tunjangan - Kasbon)
     */
    public function calculateTransfer(string $id)
    {
        $salary = Salary::with(['user', 'user.allowance'])->find($id);

        if (!$salary || !$salary->user) {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Data gaji karyawan tidak ditemukan.',
            ]);
        }

        if ($salary->status !== 'active') {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Status gaji karyawan ini nonaktif (inactive). Tidak dapat melakukan transfer.',
            ]);
        }

        $user           = $salary->user;
        $month          = now()->month;
        $year           = now()->year;
        $baseSalary     = (float) $salary->base_salary;
        $allowances     = $user->allowance;
        $totalAllowance = (float) $allowances->sum('amount');

        // Ambil kasbon aktif / approved / transferred di bulan berjalan
        $cashAdvances = CashAdvance::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'transferred'])
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        $totalCashAdvance = (float) $cashAdvances->sum('amount');
        $adminFee         = (float) Setting::get('admin_fee_disbursement', 0);
        $subtotalIncome   = $baseSalary + $totalAllowance;
        $netSalaryXendit  = max(0, $subtotalIncome - $totalCashAdvance - $adminFee);
        $netSalaryManual  = max(0, $subtotalIncome - $totalCashAdvance - $adminFee);
        $totalDeductions  = $totalCashAdvance + $adminFee;
        $netSalary        = $netSalaryXendit;

        // Ambil saldo website saat ini dari Finance API
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
                'salary_id'              => $salary->id,
                'user_id'                => $user->id,
                'user_name'              => $user->name,
                'user_phone'             => $user->phone,
                'bank_name'              => $user->bank_name,
                'account_number'         => $accountNumber = $user->account_number,
                'account_holder_name'    => $user->account_holder_name ?? $user->name,
                'base_salary'            => $baseSalary,
                'allowances'             => $allowances->map(fn($alw) => [
                    'name'   => $alw->name,
                    'amount' => (float) $alw->amount,
                ]),
                'total_allowance'        => $totalAllowance,
                'subtotal_income'        => $subtotalIncome,
                'cash_advances'          => $cashAdvances->map(fn($ca) => [
                    'id'           => $ca->id,
                    'title'        => $ca->title,
                    'amount'       => (float) $ca->amount,
                    'request_date' => Carbon::parse($ca->request_date)->translatedFormat('d F Y'),
                    'status'       => $ca->status,
                ]),
                'total_cash_advance'     => $totalCashAdvance,
                'admin_fee'              => $adminFee,
                'total_deductions'       => $totalDeductions,
                'net_salary'             => $netSalary,
                'net_salary_xendit'      => $netSalaryXendit,
                'net_salary_manual'      => $netSalaryManual,
                'balance_manual'         => $balanceManual,
                'balance_xendit'         => $balanceXendit,
                'total_balance'          => $totalBalance,
                'channel_status'         => $balRes['channel_status'] ?? ['manual' => true, 'xendit' => true],
                'channel_manual_enabled' => $balRes['channel_manual_enabled'] ?? true,
                'channel_xendit_enabled' => $balRes['channel_xendit_enabled'] ?? true,
                'current_balance'        => $balanceVal,
                'month_name'             => Carbon::now()->translatedFormat('F Y'),
            ],
        ]);
    }

    /**
     * Memproses transfer gaji ke Xendit / Manual & potong saldo website
     */
    public function processTransfer(Request $request, string $id)
    {
        $salary = Salary::with(['user', 'user.allowance'])->find($id);

        if (!$salary || !$salary->user) {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Data gaji karyawan tidak ditemukan.',
            ]);
        }

        if ($salary->status !== 'active') {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Status gaji karyawan ini nonaktif (inactive). Tidak dapat melakukan transfer.',
            ]);
        }

        $transferType   = $request->input('transfer_type', 'xendit'); // 'xendit' atau 'manual'
        $user           = $salary->user;
        $month          = now()->month;
        $year           = now()->year;
        $baseSalary     = (float) $salary->base_salary;
        $totalAllowance = (float) $user->allowance->sum('amount');

        // Hitung kasbon
        $totalCashAdvance = (float) CashAdvance::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'transferred'])
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->sum('amount');

        // Biaya admin berlaku untuk kedua tipe pembayaran (xendit & manual)
        $adminFee       = (float) Setting::get('admin_fee_disbursement', 0);
        $subtotalIncome = $baseSalary + $totalAllowance;
        $netSalary      = max(0, $subtotalIncome - $totalCashAdvance - $adminFee);

        if ($netSalary <= 0) {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Nominal gaji bersih Rp 0 atau minus (setelah dipotong kasbon & biaya admin). Tidak dapat melakukan transfer.',
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDASI 0: CEK APAKAH PERIODE BULAN INI SUDAH DITRANSFER / PENDING
        // ─────────────────────────────────────────────────────────────────
        $existingPayment = SalaryPayment::where('salary_id', $salary->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->whereIn('status', ['transferred', 'pending'])
            ->first();

        if ($existingPayment) {
            return response()->json([
                'code'    => 400,
                'status'  => false,
                'message' => 'Gaji karyawan untuk periode bulan ini sudah berstatus ' . strtoupper($existingPayment->status) . ' (' . ($existingPayment->payment_type === 'manual' ? 'Saldo Manual' : 'Saldo Xendit') . ').',
            ]);
        }

        // Update rekening bank di user jika tipe transfer adalah Xendit
        $bankName          = $request->input('bank_name', $user->bank_name);
        $accountNumber     = $request->input('account_number', $user->account_number);
        $accountHolderName = $request->input('account_holder_name', $user->account_holder_name ?? $user->name);

        if ($transferType === 'xendit' && $bankName && $accountNumber) {
            $user->update([
                'bank_name'           => $bankName,
                'account_number'      => $accountNumber,
                'account_holder_name' => $accountHolderName,
            ]);
        }

        // ─────────────────────────────────────────────────────────────────
        // VALIDASI 1: CEK SALDO WEBSITE FINANCE API (Sesuai Tipe Saldo & Channel Status)
        // ─────────────────────────────────────────────────────────────────
        $financeApi = app(FinanceApiService::class);
        if ($financeApi->isConfigured()) {
            $balRes        = $financeApi->getBalance();
            $balManual     = (float) ($balRes['balance_manual'] ?? 0);
            $balXendit     = (float) ($balRes['balance_xendit'] ?? 0);
            $manualEnabled = (bool) ($balRes['channel_manual_enabled'] ?? true);
            $xenditEnabled = (bool) ($balRes['channel_xendit_enabled'] ?? true);

            if ($transferType === 'manual') {
                if (!$manualEnabled) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => false,
                        'message' => 'Saluran Saldo Manual sedang dinonaktifkan oleh administrator Finance API.',
                    ]);
                }
                if ($balManual < $netSalary) {
                    return response()->json([
                        'code'            => 400,
                        'status'          => false,
                        'message'         => 'Saldo Manual Tidak Cukup (Tersedia: Rp ' . number_format($balManual, 0, ',', '.') . ', Dibutuhkan: Rp ' . number_format($netSalary, 0, ',', '.') . ')',
                        'current_balance' => $balManual,
                        'required_amount' => $netSalary,
                    ]);
                }
            } else {
                if (!$xenditEnabled) {
                    return response()->json([
                        'code'    => 400,
                        'status'  => false,
                        'message' => 'Saluran Saldo Xendit sedang dinonaktifkan oleh administrator Finance API.',
                    ]);
                }
                if ($balXendit < $netSalary) {
                    return response()->json([
                        'code'            => 400,
                        'status'          => false,
                        'message'         => 'Saldo Xendit Tidak Cukup (Tersedia: Rp ' . number_format($balXendit, 0, ',', '.') . ', Dibutuhkan: Rp ' . number_format($netSalary, 0, ',', '.') . ')',
                        'current_balance' => $balXendit,
                        'required_amount' => $netSalary,
                    ]);
                }
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 2: KIRIM DISBURSEMENT XENDIT (Hanya jika transfer_type == 'xendit')
        // ─────────────────────────────────────────────────────────────────
        $isXendit       = ($transferType === 'xendit');
        $xenditService  = app(XenditDisbursementService::class);
        $now            = Carbon::now();
        $externalId     = ($isXendit ? 'GAJI-' : 'MANUAL-') . $salary->id . '-' . $user->id . '-' . $now->timestamp;
        $description    = 'Gaji ' . $now->translatedFormat('F Y') . ' - ' . $user->name . ($isXendit ? '' : ' (Transfer Manual)');

        $disbursementId = null;
        $xenditStatus   = null;

        if ($isXendit && $xenditService->isConfigured()) {
            $disbResult = $xenditService->sendDisbursement(
                externalId:         $externalId,
                bankCode:           $bankName,
                accountNumber:      $accountNumber,
                accountHolderName:  $accountHolderName,
                amount:             $netSalary,
                description:        $description
            );

            if (!$disbResult['success']) {
                Log::warning('[Salary Transfer] Xendit disbursement failed', [
                    'salary_id'      => $salary->id,
                    'user_id'        => $user->id,
                    'xendit_message' => $disbResult['message'],
                ]);

                return response()->json([
                    'code'    => 400,
                    'status'  => false,
                    'message' => 'Gagal melakukan transfer gaji via Xendit: ' . $disbResult['message'],
                ]);
            }

            $disbursementId = $disbResult['disbursement_id'] ?? null;
            $xenditStatus   = $disbResult['status'] ?? 'PENDING';
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 3: SIMPAN RIWAYAT PEMBAYARAN GAJI
        // ─────────────────────────────────────────────────────────────────
        $salaryPayment = SalaryPayment::create([
            'salary_id'              => $salary->id,
            'user_id'                => $user->id,
            'payment_type'           => $transferType,
            'transferred_by'         => auth()->id(),
            'period_month'           => $now->month,
            'period_year'            => $now->year,
            'base_salary'            => $baseSalary,
            'total_allowance'        => $totalAllowance,
            'total_cash_advance'     => $totalCashAdvance,
            'net_salary'             => $netSalary,
            'bank_name'              => $bankName,
            'account_number'         => $accountNumber,
            'account_holder_name'    => $accountHolderName,
            'xendit_external_id'     => $externalId,
            'xendit_disbursement_id' => $disbursementId,
            'xendit_status'          => $xenditStatus,
            'status'                 => ($isXendit && $xenditService->isConfigured()) ? 'pending' : 'transferred',
            'transfer_at'            => ($isXendit && $xenditService->isConfigured()) ? null : $now,
            'notes'                  => $description . ($adminFee > 0 ? ' (Biaya Admin: Rp ' . number_format($adminFee, 0, ',', '.') . ')' : '') . ($isXendit ? '' : ' [Transfer Saldo Manual]'),
        ]);

        Log::info('[Salary Transfer] Payment record saved', [
            'salary_payment_id' => $salaryPayment->id,
            'salary_id'         => $salary->id,
            'user'              => $user->name,
            'payment_type'      => $transferType,
            'net_salary'        => $netSalary,
            'admin_fee'         => $adminFee,
            'xendit_external_id'=> $externalId,
        ]);

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 4: POTONG SALDO WEBSITE FINANCE SESUAI TIPE SALDO
        // ─────────────────────────────────────────────────────────────────
        if ($financeApi->isConfigured()) {
            $financeApi->deductBalance(
                amount:      $netSalary,
                referenceId: $externalId,
                description: 'Pembayaran gaji karyawan: ' . $user->name . ' periode ' . $now->translatedFormat('F Y') . ($isXendit ? '' : ' (Manual)') . ($adminFee > 0 ? ' (terpotong biaya admin Rp ' . number_format($adminFee, 0, ',', '.') . ')' : ''),
                category:    'gaji',
                note:        'Gaji Pokok: Rp ' . number_format($baseSalary) . ', Tunjangan: Rp ' . number_format($totalAllowance) . ', Potongan Kasbon: Rp ' . number_format($totalCashAdvance) . ($adminFee > 0 ? ', Potongan Admin: Rp ' . number_format($adminFee) : ''),
                balanceType: $transferType
            );
        }

        // ─────────────────────────────────────────────────────────────────
        // LANGKAH 5: NOTIFIKASI EMAIL & WHATSAPP (Jika Manual / Non-Xendit)
        // ─────────────────────────────────────────────────────────────────
        if (!$isXendit || !$xenditService->isConfigured()) {
            $monthYearStr = $now->translatedFormat('F Y');
            $dateStr      = $now->translatedFormat('l, d F Y - H:i');

            // 1. Kirim Email Slip Gaji
            if (Setting::get('notify_salary_email', '1') == '1' && !empty($user->email)) {
                try {
                    $invoiceUrl = route('salary.payment.invoice', $salaryPayment->id);

                    Mail::to($user->email)->send(new SalaryTransferredMail(
                        employee:          $user,
                        monthYearStr:      $monthYearStr,
                        baseSalary:        $baseSalary,
                        totalAllowance:    $totalAllowance,
                        totalCashAdvance:  $totalCashAdvance,
                        netSalary:         $netSalary,
                        bankName:          $bankName,
                        accountNumber:     $accountNumber,
                        accountHolderName: $accountHolderName,
                        dateStr:           $dateStr,
                        invoiceUrl:        $invoiceUrl,
                        paymentType:       $transferType
                    ));
                } catch (\Throwable $th) {
                    Log::warning('[Salary Email] Gagal kirim email transfer gaji: ' . $th->getMessage());
                }
            }

            // 2. Kirim WhatsApp Slip Gaji
            $phoneUser = $user->phone ? MekariQontakService::formatPhone($user->phone) : null;
            $qontak    = app(MekariQontakService::class);
            if (Setting::get('notify_salary_wa', '1') == '1' && $qontak->isConfigured() && $phoneUser) {
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
            }
        }

        return response()->json([
            'code'    => 200,
            'status'  => true,
            'message' => 'Pembayaran transfer gaji Rp ' . number_format($netSalary, 0, ',', '.') . ' (' . ($transferType === 'manual' ? 'Saldo Manual' : 'Saldo Xendit') . ') berhasil diproses.',
        ]);
    }

    /**
     * Tampilkan invoice / bukti transfer gaji karyawan
     */
    public function invoice(string $id)
    {
        $payment = SalaryPayment::with(['user', 'transferredBy', 'salary'])->find($id);

        if (!$payment) {
            abort(404, 'Data pembayaran transfer gaji tidak ditemukan.');
        }

        // Karyawan hanya bisa melihat invoice miliknya sendiri, Admin bisa melihat semua
        if (!auth()->user()->hasRole('Admin') && auth()->id() !== $payment->user_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti transfer ini.');
        }

        $companie = Companie::first();

        return view('pages.salarie.invoice', compact('payment', 'companie'));
    }
}
