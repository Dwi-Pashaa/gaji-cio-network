<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\CashAdvance;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserAllownce;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $salary = Salary::with(['user', 'user.allowance'])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->paginate($sort);

        return view("pages.salarie.index", compact("user", "allowance", "salary"));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "user_id"        => "required|integer",
            "base_salary"    => "required|string",
            "effective_date" => "required|date",
            "status"         => "required|string",
            "allowance_id"   => "array|nullable",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code'   => 400,
                'status' => false,
                'errors' => $validation->errors()
            ]);
        }

        $post = $request->only('user_id', 'effective_date', 'status');
        $base_salary = preg_replace('/[^0-9]/', '', $request->base_salary);
        $post['base_salary'] = $base_salary;

        Salary::create($post);

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
            'message' => 'Berhasil menyimpan data.'
        ]);
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
            "user_id"        => "required|integer",
            "base_salary"    => "required|string",
            "effective_date" => "required|date",
            "status"         => "required|string",
            "allowance_id"   => "array|nullable",
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
            'message' => 'Berhasil mengupdate data.'
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
}
