<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\AttandanceSetting;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\CashAdvanceType;
use App\Models\Salary;
use App\Models\UserAllownce;
use App\Models\UserWorkDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $setting = DB::table('setting')->latest()->first();

        if ($setting->value === 'active') {
            return $this->isOnAbsen($user);
        } else {
            return $this->isOffAbsen($user);
        }
    }

    private function isOnAbsen($user)
    {
        if ($user->can('slip gaji karyawan')) {

            $user = Auth::user();

            $setting = DB::table('setting')->latest()->first();

            $attandaceSetting = AttandanceSetting::latest()->first();

            $attendanceStartDate = \Carbon\Carbon::parse($setting->created_at)
                ->startOfMonth();

            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount');

            $month = now()->month;
            $year  = now()->year;

            $cashAdvance = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $cashAdvancePerMonth = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->selectRaw('DATE_FORMAT(request_date,"%Y-%m") as ym, SUM(amount) as total_cash_advance')
                ->groupBy('ym')
                ->pluck('total_cash_advance', 'ym');

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

                $key   = $start->format('Y-m');
                $bulan = $start->month;
                $tahun = $start->year;

                $cashAdvanceMonth = $cashAdvancePerMonth[$key] ?? 0;

                $alpha = 0;
                $cuti  = 0;
                $telat = 0;
                $attendancePenalty = 0;

                if ($start->greaterThanOrEqualTo($attendanceStartDate)) {
                    $workdays = UserWorkDay::where('user_id', $user->id)
                        ->pluck('weekday')
                        ->toArray();

                    $attendance = Attendance::where('user_id', $user->id)
                        ->whereYear('date', $tahun)
                        ->whereMonth('date', $bulan)
                        ->get()
                        ->keyBy('date');

                    $daysInMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->daysInMonth;

                    for ($d = 1; $d <= $daysInMonth; $d++) {

                        $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                        $weekday = date('w', strtotime($tanggal));

                        if (! in_array($weekday, $workdays)) continue;

                        if ($tanggal > now()->toDateString()) continue;

                        if (! isset($attendance[$tanggal])) {
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

            return view(
                "pages.dashboard.index",
                compact(
                    "salaryHistory",
                    "baseSalary",
                    "totalAllowance",
                    "cashAdvance",
                    "netSalary",
                    "currentAttendancePenalty"
                )
            );
        } else {

            $type = CashAdvanceType::all();
            return view("pages.dashboard.index", compact("type"));
        }
    }

    private function isOffAbsen($user)
    {
        if ($user->can('slip gaji karyawan')) {
            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount');

            $month = now()->month;
            $year = now()->year;

            $cashAdvance = CashAdvance::where('user_id', $user->id)->where('status', 'approved')
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $cashAdvancePerMonth = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->selectRaw('DATE_FORMAT(request_date, "%Y-%m") as ym, SUM(amount) as total_cash_advance')
                ->groupBy('ym')
                ->pluck('total_cash_advance', 'ym');

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

                $months->push([
                    'year'        => $start->format('Y'),
                    'month'       => $start->format('m'),
                    'base_salary' => $baseSalary,
                    'allowance'   => $totalAllowance,
                    'cash_advance' => $cashAdvance,
                    'net_salary'  => $baseSalary + $totalAllowance - $cashAdvance,
                ]);

                $start->addMonth();
            }

            $salaryHistory = $months;

            $netSalary = $baseSalary + $totalAllowance - $cashAdvance;
            return view("pages.dashboard.index", compact("salaryHistory", "baseSalary", "totalAllowance", "cashAdvance", "netSalary"));
        } else {
            $type = CashAdvanceType::all();
            return view("pages.dashboard.index", compact("type"));
        }
    }

    public function slip($month, $year)
    {
        $setting = DB::table('setting')->latest()->first();

        if ($setting->value === 'active') {
            return $this->slipAbsenOn($month, $year);
        } else {
            return $this->slipAbsenOff($month, $year);
        }
    }

    private function slipAbsenOn($month, $year)
    {
        $user = Auth::user();

        $setting = DB::table('setting')->latest()->first();
        $attendanceStartDate = \Carbon\Carbon::parse($setting->created_at)
            ->startOfMonth();

        $slipDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();

        $baseSalary     = $user->salary->base_salary ?? 0;
        $allowances     = $user->allowance;
        $totalAllowance = $allowances->sum('amount');

        $attandaceSetting = AttandanceSetting::latest()->first();

        $alpha = 0;
        $cuti  = 0;
        $telat = 0;
        $attendancePenalty = 0;

        if ($slipDate->greaterThanOrEqualTo($attendanceStartDate)) {

            // hari kerja user (format harus 0–6)
            $workdays = UserWorkDay::where('user_id', $user->id)
                ->pluck('weekday')
                ->toArray();

            $attendance = Attendance::where('user_id', $user->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(function ($item) {
                    return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                });

            $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {

                $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $weekday = date('w', strtotime($tanggal));

                if (! in_array($weekday, $workdays)) {
                    continue;
                }

                if ($tanggal > now()->toDateString()) {
                    continue;
                }

                if (! isset($attendance[$tanggal])) {
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

        $cashAdvance = CashAdvance::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        $cashAdvanceTotal = $cashAdvance->sum('amount');

        $netSalary = $baseSalary
            + $totalAllowance
            - $cashAdvanceTotal
            - $attendancePenalty;

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
            'potongan_alpa'     => $attandaceSetting->alpha ?? 0,
            'potongan_cuti'     => $attandaceSetting->cuti ?? 0,
            'potongan_telat'    => $attandaceSetting->telat ?? 0,
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
