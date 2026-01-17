<?php

namespace App\Http\Controllers\Pages\Absen;

use App\Http\Controllers\Controller;
use App\Models\AttandanceSetting;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Koordinat;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserWorkDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $userId = auth()->id();

        $attandanceSetting = AttandanceSetting::where('user_id', Auth::id())->first();

        $attendanceStartDate = \Carbon\Carbon::parse(
            DB::table('setting')->where('user_id', Auth::id())->value('created_at')
        )->startOfDay();

        $attandance = Attendance::where('user_id', $userId)
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            })
            ->get()
            ->keyBy('date');

        $workdays = UserWorkDay::where('user_id', $userId)
            ->pluck('weekday')
            ->toArray();

        $bulan = $start
            ? \Carbon\Carbon::parse($start)->month
            : now()->month;

        $tahun = $start
            ? \Carbon\Carbon::parse($start)->year
            : now()->year;

        $jumlahHari = \Carbon\Carbon::create($tahun, $bulan, 1)->daysInMonth;
        $hariPertama = \Carbon\Carbon::create($tahun, $bulan, 1)->dayOfWeekIso;

        $alpha = 0;

        for ($d = 1; $d <= $jumlahHari; $d++) {

            $tanggal = \Carbon\Carbon::create($tahun, $bulan, $d);

            if ($tanggal->lt($attendanceStartDate)) {
                continue;
            }

            if ($tanggal->gt(now())) {
                continue;
            }

            if (! in_array($tanggal->dayOfWeek, $workdays)) {
                continue;
            }

            if (isset($attandance[$tanggal->toDateString()])) {

                $status = $attandance[$tanggal->toDateString()]->status;

                if (in_array($status, ['izin', 'cuti', 'sakit'])) {
                    continue;
                }

                continue;
            }

            $alpha++;
        }

        return view('pages.absen.list.index', compact(
            'attandance',
            'attandanceSetting',
            'workdays',
            'bulan',
            'tahun',
            'jumlahHari',
            'hariPertama',
            'alpha',
            'attendanceStartDate'
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

        $attendanceSetting = AttandanceSetting::where('user_id', $userId)->first();

        if (! $attendanceSetting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengaturan absensi belum tersedia.'
            ], 422);
        }

        $location = Koordinat::find($attendanceSetting->koordinat_id);

        if (! $location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi absensi tidak ditemukan.'
            ], 422);
        }

        $checkInTime = Carbon::parse($validated['check_in'] ?? now());
        $startTime   = Carbon::parse($attendanceSetting->start_work);
        $endTime     = Carbon::parse($attendanceSetting->end_work);

        $distance = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            $location->lat,
            $location->lng
        );

        $distanceMeter = round($distance);
        $radiusMeter   = $location->radius;

        if ($distanceMeter > $radiusMeter) {
            return response()->json([
                'status' => 'error',
                'message' => "Anda, berada dalam jarak {$distanceMeter} Meter, absensi yang dibolehkan adalah dalam radius {$radiusMeter} Meter"
            ], 422);
        }

        if ($checkInTime->lt($startTime)) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Belum waktu absen — jam kerja belum dimulai.'
            ], 422);
        }

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

        $validated['status'] = 'hadir';
        $validated['user_id'] = $userId;

        Attendance::create($validated);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Absen berhasil — Anda tepat waktu.'
        ], 200);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function rekap(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        $attandaceSetting = AttandanceSetting::latest()->first();

        $users = User::with(['salary', 'allowance'])
            ->whereIn('id', function ($query) {
                $query->select('user_id')
                    ->from('setting')
                    ->where('key', 'fiture_absen')
                    ->where('value', 'active');
            })
            ->get();

        $salaryHistoryAll = collect();

        foreach ($users as $user) {

            $setting = DB::table('setting')
                ->where('user_id', $user->id)
                ->where('key', 'fiture_absen')
                ->where('value', 'active')
                ->first();

            if (! $setting) continue;

            $attendanceStartDate = \Carbon\Carbon::parse($setting->created_at)->startOfDay();

            $baseSalary     = $user->salary->base_salary ?? 0;
            $totalAllowance = $user->allowance->sum('amount') ?? 0;

            $cashAdvanceMonth = CashAdvance::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereMonth('request_date', $month)
                ->whereYear('request_date', $year)
                ->sum('amount');

            $hadir  = 0;
            $alpha  = 0;
            $cuti   = 0;
            $telat  = 0;
            $attendancePenalty = 0;

            $startOfMonth = \Carbon\Carbon::create($year, $month, 1);
            $endOfMonth   = $startOfMonth->copy()->endOfMonth();

            // 👉 bulan sudah masuk masa absensi
            if ($endOfMonth->greaterThanOrEqualTo($attendanceStartDate)) {

                $workdays = UserWorkDay::where('user_id', $user->id)
                    ->pluck('weekday')
                    ->toArray();

                $attendance = Attendance::where('user_id', $user->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get()
                    ->keyBy('date');

                $daysInMonth = $startOfMonth->daysInMonth;

                for ($d = 1; $d <= $daysInMonth; $d++) {

                    $tanggal = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $tanggalCarbon = \Carbon\Carbon::parse($tanggal);
                    $weekday = date('w', strtotime($tanggal));

                    if (! in_array($weekday, $workdays)) continue;

                    if ($tanggalCarbon->gt(now())) continue;

                    if ($tanggalCarbon->lt($attendanceStartDate)) continue;

                    if (! isset($attendance[$tanggal])) {
                        $alpha++;
                    }
                }

                $hadir  = $attendance->where('status', 'hadir')->count();
                $cuti   = $attendance->where('status', 'izin')->count();
                $telat  = $attendance->where('status', 'terlambat')->count();

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
