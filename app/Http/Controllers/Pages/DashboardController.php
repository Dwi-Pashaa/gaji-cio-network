<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\AttandanceSetting;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\CashAdvanceType;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\UserAllownce;
use App\Models\UserWorkDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        if ($role === 'Admin') {
            return $this->isAdmin();
        }

        $setting = DB::table('setting')
            ->where('user_id', $user->id)
            ->whereNotNull('user_id')
            ->orderByDesc('id')
            ->first();

        if (! $setting) {
            return $this->isOffAbsen($user);
        }

        if ($setting->value !== 'active') {
            return $this->isOffAbsen($user);
        }

        return $this->isOnAbsen($user);
    }

    private function isAdmin()
    {
        $financeApi = app(\App\Services\FinanceApiService::class);
        $financeBalance = $financeApi->getBalance();

        // Statistik Karyawan
        $totalEmployees = \App\Models\User::role('Karyawan')->count();
        if ($totalEmployees === 0) {
            $totalEmployees = \App\Models\User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            })->count();
        }

        // Statistik Kasbon
        $kasbonApprovedCount  = CashAdvance::where('status', 'approved')->orWhere('status', 'transferred')->count();
        $kasbonApprovedAmount = CashAdvance::whereIn('status', ['approved', 'transferred'])->sum('amount');
        $kasbonRejectedCount  = CashAdvance::where('status', 'rejected')->count();
        $kasbonRejectedAmount = CashAdvance::where('status', 'rejected')->sum('amount');
        $kasbonPendingCount   = CashAdvance::where('status', 'pending')->count();

        // Statistik Pembayaran Gaji Bulan Berjalan
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $totalSalaryPaidMonth = \App\Models\SalaryPayment::where('status', 'transferred')
            ->where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->sum('net_salary');

        $salaryPaidCount = \App\Models\SalaryPayment::where('status', 'transferred')
            ->where('period_month', $currentMonth)
            ->where('period_year', $currentYear)
            ->count();

        // Transaksi Pembayaran Gaji Terbaru (5 transaksi terakhir)
        $recentSalaryPayments = \App\Models\SalaryPayment::with('user')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        // Kasbon Terbaru yang Perlu Tindakan / Baru (5 transaksi)
        $recentCashAdvances = CashAdvance::with('user')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        return view("pages.dashboard.index", compact(
            "financeBalance",
            "financeApi",
            "totalEmployees",
            "kasbonApprovedCount",
            "kasbonApprovedAmount",
            "kasbonRejectedCount",
            "kasbonRejectedAmount",
            "kasbonPendingCount",
            "totalSalaryPaidMonth",
            "salaryPaidCount",
            "recentSalaryPayments",
            "recentCashAdvances"
        ));
    }

    private function isOnAbsen($user)
    {
        if ($user->can('slip gaji karyawan')) {

            $user = Auth::user();

            $setting = DB::table('setting')->where('user_id', Auth::id())->first();
            $attandaceSetting = AttandanceSetting::where('user_id', Auth::id())->first();

            $attendanceStartDate = Carbon::parse($setting->created_at)->startOfDay();

            $baseSalary      = $user->salary->base_salary ?? 0;
            $totalAllowance  = $user->allowance->sum('amount');

            $month = now()->month;
            $year  = now()->year;

            $cashAdvance = CashAdvance::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'transferred'])
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $cashAdvancePerMonth = CashAdvance::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'transferred'])
                ->selectRaw('DATE_FORMAT(request_date,"%Y-%m") as ym, SUM(amount) as total_cash_advance')
                ->groupBy('ym')
                ->pluck('total_cash_advance', 'ym');

            $salaryPayments = SalaryPayment::where('user_id', $user->id)
                ->get()
                ->keyBy(function ($item) {
                    return sprintf('%04d-%02d', $item->period_year, $item->period_month);
                });

            $firstSalary = Salary::where('user_id', $user->id)
                ->orderBy('effective_date', 'asc')
                ->first();

            if ($firstSalary) {
                $start = Carbon::parse($firstSalary->effective_date)->startOfMonth();
            } else {
                $start = now()->startOfMonth();
            }

            $end = now()->endOfMonth();
            $months = collect();

            while ($start <= $end) {

                $key   = $start->format('Y-m');
                $bulan = $start->month;
                $tahun = $start->year;

                $cashAdvanceMonth = $cashAdvancePerMonth[$key] ?? 0;
                $payment = $salaryPayments[$key] ?? null;

                $alpha = 0;
                $cuti  = 0;
                $telat = 0;
                $attendancePenalty = 0;

                // 👉 BULAN INI SUDAH MASUK MASA ABSEN
                if ($start->endOfMonth()->greaterThanOrEqualTo($attendanceStartDate)) {

                    $workdays = UserWorkDay::where('user_id', $user->id)
                        ->pluck('weekday')
                        ->toArray();

                    $attendance = Attendance::where('user_id', $user->id)
                        ->whereYear('date', $tahun)
                        ->whereMonth('date', $bulan)
                        ->get()
                        ->keyBy('date');

                    $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;

                    for ($d = 1; $d <= $daysInMonth; $d++) {

                        $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                        $tanggalCarbon = Carbon::parse($tanggal);
                        $weekday = date('w', strtotime($tanggal));

                        // ❌ Bukan hari kerja
                        if (!in_array($weekday, $workdays)) continue;

                        // ❌ Tanggal masa depan
                        if ($tanggalCarbon->gt(now())) continue;

                        // 🔥 SEBELUM SETTING → JANGAN DIHITUNG
                        if ($tanggalCarbon->lt($attendanceStartDate)) continue;

                        if (!isset($attendance[$tanggal])) {
                            $alpha++;
                        }
                    }

                    $cuti  = $attendance->where('status', 'izin')->count();
                    $telat = $attendance->where('status', 'terlambat')->count();

                    $attendancePenalty =
                        ($alpha * ($attandaceSetting->alpha ?? 0)) +
                        ($cuti  * ($attandaceSetting->cuti ?? 0)) +
                        ($telat * ($attandaceSetting->telat ?? 0));
                }

                $netSalaryMonth = $baseSalary
                    + $totalAllowance
                    - $cashAdvanceMonth
                    - $attendancePenalty;

                $months->push([
                    'year'               => $start->format('Y'),
                    'month'              => $start->format('m'),
                    'base_salary'        => $baseSalary,
                    'allowance'          => $totalAllowance,
                    'cash_advance'       => $cashAdvanceMonth,
                    'alpha'              => $alpha,
                    'cuti'               => $cuti,
                    'telat'              => $telat,
                    'attendance_penalty' => $attendancePenalty,
                    'net_salary'         => $netSalaryMonth,
                    'payment'            => $payment,
                    'payment_status'     => $payment ? $payment->status : null,
                    'payment_id'         => $payment ? $payment->id : null,
                ]);

                $start->addMonth();
            }

            $salaryHistory = $months;
            $currentMonthData = $salaryHistory->last();

            $currentAttendancePenalty = $currentMonthData['attendance_penalty'] ?? 0;

            $netSalary = $baseSalary
                + $totalAllowance
                - $cashAdvance
                - $currentAttendancePenalty;

            $currentMonthKey = now()->format('Y-m');
            $currentPayment  = $salaryPayments[$currentMonthKey] ?? null;

            $isAbsenOn = true;
            return view(
                "pages.dashboard.index",
                compact(
                    "isAbsenOn",
                    "salaryHistory",
                    "baseSalary",
                    "totalAllowance",
                    "cashAdvance",
                    "netSalary",
                    "currentAttendancePenalty",
                    "currentPayment"
                )
            );
        }
    }

    private function isOffAbsen($user)
    {
        if ($user->can('slip gaji karyawan')) {
            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount');

            $month = now()->month;
            $year = now()->year;

            $cashAdvance = CashAdvance::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'transferred'])
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $cashAdvancePerMonth = CashAdvance::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'transferred'])
                ->selectRaw('DATE_FORMAT(request_date, "%Y-%m") as ym, SUM(amount) as total_cash_advance')
                ->groupBy('ym')
                ->pluck('total_cash_advance', 'ym');

            $salaryPayments = SalaryPayment::where('user_id', $user->id)
                ->get()
                ->keyBy(function ($item) {
                    return sprintf('%04d-%02d', $item->period_year, $item->period_month);
                });

            $firstSalary = Salary::where('user_id', $user->id)
                ->orderBy('effective_date', 'asc')
                ->first();

            if ($firstSalary) {
                $start = \Carbon\Carbon::parse($firstSalary->effective_date)->startOfMonth();
            } else {
                $start = now()->startOfMonth();
            }

            $end = now()->endOfMonth();

            $months = collect();

            while ($start <= $end) {
                $key = $start->format('Y-m');
                $cashAdvance = $cashAdvancePerMonth[$key] ?? 0;
                $payment     = $salaryPayments[$key] ?? null;

                $months->push([
                    'year'           => $start->format('Y'),
                    'month'          => $start->format('m'),
                    'base_salary'    => $baseSalary,
                    'allowance'      => $totalAllowance,
                    'cash_advance'   => $cashAdvance,
                    'net_salary'     => $baseSalary + $totalAllowance - $cashAdvance,
                    'payment'        => $payment,
                    'payment_status' => $payment ? $payment->status : null,
                    'payment_id'     => $payment ? $payment->id : null,
                ]);

                $start->addMonth();
            }

            $salaryHistory = $months;

            $netSalary = $baseSalary + $totalAllowance - $cashAdvance;
            $currentMonthKey = now()->format('Y-m');
            $currentPayment  = $salaryPayments[$currentMonthKey] ?? null;

            $isAbsenOn = false;
            return view("pages.dashboard.index", compact("isAbsenOn", "salaryHistory", "baseSalary", "totalAllowance", "cashAdvance", "netSalary", "currentPayment"));
        }
    }

    public function slip($month, $year)
    {
        $user = Auth::user();

        $setting = DB::table('setting')
            ->where('user_id', $user->id)
            ->whereNotNull('user_id')
            ->orderByDesc('id')
            ->first();

        if (! $setting) {
            return $this->slipAbsenOff($month, $year);
        }

        if ($setting->value !== 'active') {
            return $this->slipAbsenOff($month, $year);
        }

        return $this->slipAbsenOn($month, $year);
    }

    private function slipAbsenOn($month, $year)
    {
        $user = Auth::user();

        // =========================
        // SETTING ABSENSI USER
        // =========================
        $setting = DB::table('setting')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if (! $setting) {
            abort(403, 'Setting absensi belum tersedia.');
        }

        // tanggal mulai absensi (real start)
        $attendanceStartDate = Carbon::parse($setting->created_at)->startOfDay();

        // bulan slip
        $slipDate = Carbon::create($year, $month, 1)->startOfMonth();

        // =========================
        // GAJI
        // =========================
        $baseSalary     = $user->salary->base_salary ?? 0;
        $allowances     = $user->allowance;
        $totalAllowance = $allowances->sum('amount');

        // =========================
        // POTONGAN ABSEN
        // =========================
        $attendanceSetting = AttandanceSetting::where('user_id', $user->id)->first();

        $alpha = 0;
        $cuti  = 0;
        $telat = 0;
        $attendancePenalty = 0;

        // =========================
        // HITUNG ABSEN
        // =========================
        if ($slipDate->endOfMonth()->greaterThanOrEqualTo($attendanceStartDate)) {

            $today = now()->toDateString();

            // hari kerja user (0–6)
            $workdays = UserWorkDay::where('user_id', $user->id)
                ->pluck('weekday')
                ->toArray();

            // absensi bulan ini
            $attendance = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {

                $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $weekday = date('w', strtotime($tanggal));

                // ❌ bukan hari kerja
                if (! in_array($weekday, $workdays)) {
                    continue;
                }

                // ❌ sebelum absensi aktif
                if ($tanggal < $attendanceStartDate->toDateString()) {
                    continue;
                }

                $absen = $attendance[$tanggal] ?? null;

                // =========================
                // HARI INI
                // =========================
                if ($tanggal === $today && ! $absen) {
                    continue;
                }

                // =========================
                // TANGGAL MASA DEPAN
                // =========================
                if ($tanggal > $today) {

                    // ✔ hanya dihitung jika ada izin/cuti
                    if ($absen && $absen->status === 'izin') {
                        $cuti++;
                    }

                    continue;
                }

                // =========================
                // TANGGAL SUDAH LEWAT
                // =========================
                if (! $absen) {
                    $alpha++;
                    continue;
                }

                if ($absen->status === 'izin') {
                    $cuti++;
                } elseif ($absen->status === 'terlambat') {
                    $telat++;
                }
            }

            // total potongan
            $attendancePenalty =
                ($alpha * ($attendanceSetting->alpha ?? 0)) +
                ($cuti  * ($attendanceSetting->cuti ?? 0)) +
                ($telat * ($attendanceSetting->telat ?? 0));
        }

        // =========================
        // KASBON
        // =========================
        $cashAdvance = CashAdvance::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'transferred'])
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        $cashAdvanceTotal = $cashAdvance->sum('amount');

        // =========================
        // GAJI BERSIH
        // =========================
        $netSalary = $baseSalary
            + $totalAllowance
            - $cashAdvanceTotal
            - $attendancePenalty;

        // =========================
        // DATA PDF
        // =========================
        $data = [
            'user'              => $user,
            'year'              => $year,
            'month'             => $month,
            'baseSalary'        => $baseSalary,
            'allowances'        => $allowances,
            'totalAllowance'    => $totalAllowance,

            'alpha'             => $alpha,
            'cuti'              => $cuti,
            'telat'             => $telat,
            'potongan_alpa'     => $attendanceSetting->alpha ?? 0,
            'potongan_cuti'     => $attendanceSetting->cuti ?? 0,
            'potongan_telat'    => $attendanceSetting->telat ?? 0,
            'attendancePenalty' => $attendancePenalty,

            'cashAdvance'       => $cashAdvance,
            'cashAdvanceTotal'  => $cashAdvanceTotal,

            'netSalary'         => $netSalary,
        ];

        $pdf = Pdf::loadView('export.slip-salary-on', $data);
        return $pdf->stream("slip-gaji-{$user->name}-{$month}-{$year}.pdf");
    }

    private function slipAbsenOff($month, $year)
    {
        $user = Auth::user();

        $baseSalary     = $user->salary->base_salary ?? 0;
        $allowances     = $user->allowance;
        $totalAllowance = $allowances->sum('amount');

        $cashAdvance = CashAdvance::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'transferred'])
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        $cashAdvanceTotal = $cashAdvance->sum('amount');

        $netSalary = $baseSalary + $totalAllowance - $cashAdvanceTotal;

        $data = [
            'user'          => $user,
            'year'          => $year,
            'month'         => $month,
            'baseSalary'    => $baseSalary,
            'allowances'    => $allowances,
            'totalAllowance' => $totalAllowance,
            'cashAdvance'   => $cashAdvance,
            'netSalary'     => $netSalary,
            'cashAdvanceTotal' => $cashAdvanceTotal
        ];

        $pdf = Pdf::loadView('export.slip-salary-off', $data);
        return $pdf->stream("slip-gaji-{$user->name}-{$month}-{$year}.pdf");
    }
}
