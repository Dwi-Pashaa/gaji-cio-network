<?php

namespace App\Http\Controllers\Pages\Absen;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $start = $request->start ?? null;
        $end = $request->end ?? null;

        $roles = Auth::user()->getRoleNames()[0];

        if ($roles === "Admin") {
            $leaves = Leave::with('user')
                ->when($start && $end, function ($query) use ($start, $end) {
                    $query->whereDate('start_date', '>=', $start)
                        ->whereDate('end_date', '<=', $end);
                })
                ->when($start && !$end, function ($query) use ($start) {
                    $query->whereDate('start_date', '>=', $start);
                })
                ->when(!$start && $end, function ($query) use ($end) {
                    $query->whereDate('end_date', '<=', $end);
                })
                ->orderBy('id', 'DESC')
                ->paginate($sort)
                ->appends([$request->query()]);
        } else {
            $leaves = Leave::where('user_id', Auth::user()->id)
                ->when($start && $end, function ($query) use ($start, $end) {
                    $query->whereDate('start_date', '>=', $start)
                        ->whereDate('end_date', '<=', $end);
                })
                ->when($start && !$end, function ($query) use ($start) {
                    $query->whereDate('start_date', '>=', $start);
                })
                ->when(!$start && $end, function ($query) use ($end) {
                    $query->whereDate('end_date', '<=', $end);
                })
                ->orderBy('id', 'DESC')
                ->paginate($sort)
                ->appends([$request->query()]);
        }

        return view("pages.leave.index", compact('leaves'));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "tipe" => "required|string",
            "start_date" => "required|string",
            "end_date" => "required|string",
            "desc" => "nullable|string",
        ]);

        if ($validation->fails()) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "errors" => $validation->errors()
            ]);
        }

        $data = $request->except('tipe');
        $data['user_id'] = Auth::user()->id;
        $data['type'] = $request->tipe;

        Leave::create($data);

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil melakukan pengajuan izin/cuti."
        ]);
    }

    public function show($id)
    {
        $leave = Leave::find($id);

        if (! $leave) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Data tidak ditemukan."
            ]);
        }

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $leave
        ]);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            "tipe" => "required|string",
            "start_date" => "required|string",
            "end_date" => "required|string",
            "desc" => "nullable|string",
        ]);

        if ($validation->fails()) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "errors" => $validation->errors()
            ]);
        }

        $leave = Leave::find($id);

        $data = $request->except('tipe');
        $data['type'] = $request->tipe;

        $leave->update($data);

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil melakukan perubahan pengajuan izin/cuti."
        ]);
    }

    public function destroy($id)
    {
        $leave = Leave::find($id);

        if (! $leave) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Data tidak ditemukan."
            ]);
        }

        $leave->delete();

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil menghapus pengajuan izin/cuti."
        ]);
    }

    public function approved($id)
    {
        $leave = Leave::find($id);

        if (! $leave) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Data tidak ditemukan."
            ]);
        }

        $leave->update(['status' => 'approved']);

        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

        foreach ($period as $date) {

            Attendance::updateOrCreate(
                [
                    'user_id' => $leave->user_id,
                    'date'    => $date->format('Y-m-d'),
                ],
                [
                    'status' => 'izin',
                    'check_in' => null,
                    'latitude' => null,
                    'longitude' => null
                ]
            );
        }

        return response()->json([
            "code"    => 200,
            "status"  => "success",
            "message" => "Cuti/Izin berhasil disetujui & absen dibuat."
        ]);
    }

    public function rejected($id)
    {
        $leave = Leave::find($id);

        if (! $leave) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "Data tidak ditemukan."
            ]);
        }

        $leave->update(['status' => 'rejected']);

        return response()->json([
            "code"    => 200,
            "status"  => "success",
            "message" => "Cuti/Izin berhasil ditolak."
        ]);
    }
}
