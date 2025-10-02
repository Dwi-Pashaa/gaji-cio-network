<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\Companie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view("pages.cash-advance.index", compact("cashAdvance"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "title" => "required",
            "amount" => "required",
        ]);

        if ($validation->fails()) {
            return back()->withErrors($validation)->withInput();
        }

        $user = Auth::user();
        $requestDate = Carbon::now();

        $amount = preg_replace('/[^0-9]/', '', $request->amount);

        CashAdvance::create([
            "user_id" => $user->id,
            "request_date" => $requestDate,
            "amount" => $amount,
            "title" => $request->title,
        ]);

        $message = "Pengajuan Kasbon\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$request->title}\n"
            . "Jumlah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Hari & Tanggal: " . $requestDate->translatedFormat('l, d F Y');

        $companie = Companie::latest()->first();
        $telp = $companie->telp;

        $waNumber = preg_replace('/^0/', '62', $telp);

        $waLink = "https://wa.me/{$waNumber}?text=" . rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil menyimpan data.',
            'wa_link' => $waLink
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
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            "amount" => "required"
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Opps ada yang belum di isi.',
                'errors' => $validation->errors()
            ]);
        }

        $cashAdvance = CashAdvance::findOrFail($id);

        $user = Auth::user();
        $requestDate = Carbon::now();

        $amount = preg_replace('/[^0-9]/', '', $request->amount);

        $cashAdvance->update([
            "amount" => $amount,
            "title" => $request->title,
        ]);

        $message = "Perubahan Pengajuan Kasbon\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$request->title}\n"
            . "Jumlah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Hari & Tanggal: " . $requestDate->translatedFormat('l, d F Y');

        $companie = Companie::latest()->first();
        $telp = $companie->telp;

        $waNumber = preg_replace('/^0/', '62', $telp);

        $waLink = "https://wa.me/{$waNumber}?text=" . rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil mengupdate data.',
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

        $cashAdvance->delete();

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menghapus data.']);
    }

    public function approval(Request $request)
    {
        $start = $request->start ?? null;
        $end   = $request->end ?? null;
        $sort  = $request->sort ?? 10;

        $cashAdvance = CashAdvance::with(['user'])
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
        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        $cashAdvance->update([
            'status' => 'approved',
            'approved_date' => Carbon::now()
        ]);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menyimpan data.']);
    }

    public function rejected(string $id)
    {
        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        $cashAdvance->update([
            'status' => 'rejected',
            'approved_date' => Carbon::now()
        ]);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menyimpan data.']);
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
