<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\Salary;
use App\Models\UserAllownce;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

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
            return view("pages.dashboard", compact("salaryHistory", "baseSalary", "totalAllowance", "cashAdvance", "netSalary"));
        } else {
            return view("pages.dashboard");
        }
    }

    public function slip($month, $year)
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

        $pdf = Pdf::loadView('export.slip-salary', $data);
        return $pdf->stream("slip-gaji-{$user->name}-{$month}-{$year}.pdf");
    }
}
