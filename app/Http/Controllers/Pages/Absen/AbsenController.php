<?php

namespace App\Http\Controllers\Pages\Absen;

use App\Http\Controllers\Controller;
use App\Models\AttandanceSetting;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserWorkDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $start = $request->start ?? null;
        $end = $request->end ?? null;

        $setting = DB::table('setting')->latest()->first();

        if ($setting->value === 'active') {
            return $this->absenOn($request, $sort, $start, $end);
        } else {
            return $this->absenOff();
        }
    }

    private function absenOn($request, $sort, $start, $end)
    {
        $attandanceSetting = AttandanceSetting::latest()->first();

        $attandance = Attendance::where('user_id', auth()->id())
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            })
            ->get()
            ->keyBy('date');

        $workdays = UserWorkDay::where('user_id', auth()->id())
            ->pluck('weekday')
            ->toArray();

        $bulan  = date('m');
        $tahun  = date('Y');
        $jumlahHari  = date('t', strtotime("$tahun-$bulan-01"));
        $hariPertama = date('N', strtotime("$tahun-$bulan-01"));

        return view('pages.absen.list.index', compact(
            'attandance',
            'attandanceSetting',
            'workdays',
            'bulan',
            'tahun',
            'jumlahHari',
            'hariPertama'
        ));
    }

    private function absenOff()
    {
        return view('pages.absen.list.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'      => 'required|date',
            'check_in'  => 'nullable|date_format:H:i:s',
            'latitude'  => 'required|string',
            'longitude' => 'required|string',
        ]);

        $userId = auth()->id();

        $alreadyChecked = Attendance::where('user_id', $userId)
            ->where('date', $validated['date'])
            ->exists();

        if ($alreadyChecked) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Anda sudah absen hari ini.'
            ], 422);
        }

        // cek hari kerja
        $weekday = Carbon::parse($validated['date'])->dayOfWeek;

        $isWorkday = UserWorkDay::where('user_id', $userId)
            ->where('weekday', $weekday)
            ->exists();

        if (! $isWorkday) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Tidak bisa absen — hari ini bukan hari kerja Anda.'
            ], 422);
        }

        $attandanceSetting = AttandanceSetting::latest()->first();

        $checkInTime = Carbon::parse($validated['check_in'] ?? now());
        $startTime   = Carbon::parse($attandanceSetting->start_work);
        $endTime     = Carbon::parse($attandanceSetting->end_work);

        if ($checkInTime->lt($startTime)) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Belum waktu absen — jam kerja belum dimulai.'
            ], 422);
        }

        // (opsional) ❗ jika ingin blokir setelah jam kerja
        // if ($checkInTime->gt($endTime)) {
        //     return response()->json([
        //         'code' => 400,
        //         'status' => 'error',
        //         'message' => 'Absen ditutup — Anda melewati jam kerja.'
        //     ], 422);
        // }

        if ($checkInTime->gt($endTime)) {
            $lateMinutes = $checkInTime->diffInMinutes($endTime);

            $validated['status'] = 'terlambat';

            Attendance::create([
                ...$validated,
                'user_id' => $userId,
            ]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => "Anda terlambat {$lateMinutes} menit."
            ], 200);
        }

        // tepat waktu
        $validated['status'] = 'hadir';
        $validated['user_id'] = $userId;

        Attendance::create($validated);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Absen berhasil — Anda tepat waktu.'
        ], 200);
    }

    public function rekap(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        $setting = DB::table('setting')->latest()->first();
        $attendanceStartDate = \Carbon\Carbon::parse($setting->created_at)->startOfMonth();

        $attandaceSetting = AttandanceSetting::latest()->first();

        $users = User::with(['salary', 'allowance'])->get();

        $salaryHistoryAll = collect();

        foreach ($users as $user) {

            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount') ?? 0;

            // Cash advance bulan ini
            $cashAdvanceMonth = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $hadir = 0;
            $alpha = 0;
            $cuti  = 0;
            $telat = 0;
            $attendancePenalty = 0;
            $hariKerja = 0;

            // Hitung hanya jika setelah fitur absen diaktifkan
            if (\Carbon\Carbon::create($year, $month, 1)->greaterThanOrEqualTo($attendanceStartDate)) {

                $workdays = UserWorkDay::where('user_id', $user->id)
                    ->pluck('weekday')
                    ->toArray();

                $attendance = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get()
                    ->keyBy('date');

                $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;

                for ($d = 1; $d <= $daysInMonth; $d++) {

                    $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $weekday = date('w', strtotime($tanggal));

                    if (! in_array($weekday, $workdays)) continue;
                    if ($tanggal > now()->toDateString()) continue;

                    if (! isset($attendance[$tanggal])) {
                        $alpha++;
                    }
                }

                $hadir  = $attendance->where('status', 'hadir')->count();
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

            $salaryHistoryAll->push([
                'user'               => $user,
                'month'              => $month,
                'year'               => $year,
                'hadir'              => $hadir,
                'alpha'              => $alpha,
                'cuti'               => $cuti,
                'telat'              => $telat,
                'attendance_penalty' => $attendancePenalty,
                'base_salary'        => $baseSalary,
                'allowance'          => $totalAllowance,
                'cash_advance'       => $cashAdvanceMonth,
                'net_salary'         => $netSalaryMonth,
            ]);
        }

        return view("pages.absen.list.rekap", compact('salaryHistoryAll'));
    }
}
