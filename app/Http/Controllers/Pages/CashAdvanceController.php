<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Models\CashAdvanceType;
use App\Models\Companie;
use App\Models\User;
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

        $type = CashAdvanceType::all();

        return view("pages.cash-advance.index", compact("cashAdvance", "type"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "title" => "required",
            "amount" => "required",
            "type_id" => "required",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'errors' => $validation->errors()
            ]);
        }

        $user = Auth::user();
        $requestDate = Carbon::now();

        $amount = preg_replace('/[^0-9]/', '', $request->amount);

        CashAdvance::create([
            "user_id" => $user->id,
            "request_date" => $requestDate,
            "amount" => $amount,
            "title" => $request->title,
            "type_id" => $request->type_id,
        ]);

        $type = CashAdvanceType::find($request->type_id);
        if ($type) {
            $type->amount = max(0, $type->amount - $amount);
            $type->save();
        }

        $message = "Pengajuan Kasbon\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$request->title}\n"
            . "Jumlah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Tipe Pengambilan Kasbon: {$type->name}\n"
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
    public function update(Request $request, string $id)
    {
        $validation = Validator::make($request->all(), [
            "title" => "required",
            "amount" => "required",
            "type_id" => "required",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'errors' => $validation->errors()
            ]);
        }

        $cashAdvance = CashAdvance::find($id);

        if (!$cashAdvance) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Data tidak ditemukan.',
            ]);
        }

        $user = Auth::user();
        $amountBaru = preg_replace('/[^0-9]/', '', $request->amount);
        $amountLama = $cashAdvance->amount;
        $typeBaru = CashAdvanceType::find($request->type_id);
        $typeLama = CashAdvanceType::find($cashAdvance->type_id);

        if ($typeBaru && $typeLama && $typeBaru->id != $typeLama->id) {
            $typeLama->amount += $amountLama;
            $typeLama->save();

            $typeBaru->amount = max(0, $typeBaru->amount - $amountBaru);
            $typeBaru->save();
        } else {
            $selisih = $amountBaru - $amountLama;
            if ($typeBaru) {
                $typeBaru->amount = max(0, $typeBaru->amount - $selisih);
                $typeBaru->save();
            }
        }

        $cashAdvance->update([
            "title" => $request->title,
            "amount" => $amountBaru,
            "type_id" => $request->type_id,
            "updated_at" => Carbon::now(),
        ]);

        $message = "Perubahan Pengajuan Kasbon\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$request->title}\n"
            . "Jumlah Baru: Rp" . number_format($amountBaru, 0, ',', '.') . "\n"
            . "Tipe Kasbon: {$typeBaru->name}\n"
            . "Tanggal Diperbarui: " . Carbon::now()->translatedFormat('l, d F Y - H:i');

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

        $defaultPhone = "6285324780031";
        $user = User::find($cashAdvance->user_id);

        $phoneUser = $user->phone;
        if ($phoneUser) {
            $phoneUser = preg_replace('/[^0-9]/', '', $phoneUser);
            if (substr($phoneUser, 0, 1) === "0") {
                $phoneUser = "62" . substr($phoneUser, 1);
            }
        }

        $cashAdvance->update([
            'status' => 'approved',
            'approved_date' => Carbon::now()
        ]);

        $message = "Konfirmasi Kasbon Disetujui\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$cashAdvance->title}\n"
            . "Jumlah: Rp" . number_format($cashAdvance->amount, 0, ',', '.') . "\n"
            . "Tanggal Disetujui: " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $encodedMsg = rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil menyimpan data.',
            'wa_link_user' => $phoneUser ? "https://wa.me/{$phoneUser}?text={$encodedMsg}" : null,
            'wa_link_default' => "https://wa.me/{$defaultPhone}?text={$encodedMsg}"
        ]);
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

        $defaultPhone = "6285324780031";
        $user = User::find($cashAdvance->user_id);

        $phoneUser = $user->phone;
        if ($phoneUser) {
            $phoneUser = preg_replace('/[^0-9]/', '', $phoneUser);
            if (substr($phoneUser, 0, 1) === "0") {
                $phoneUser = "62" . substr($phoneUser, 1);
            }
        }

        $cashAdvance->update([
            'status' => 'rejected',
            'approved_date' => Carbon::now()
        ]);

        $message = "Konfirmasi Kasbon Ditolak\n\n"
            . "Nama: {$user->name}\n"
            . "Judul: {$cashAdvance->title}\n"
            . "Jumlah: Rp" . number_format($cashAdvance->amount, 0, ',', '.') . "\n"
            . "Tanggal Disetujui: " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $encodedMsg = rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil menyimpan data.',
            'wa_link_user' => $phoneUser ? "https://wa.me/{$phoneUser}?text={$encodedMsg}" : null,
            'wa_link_default' => "https://wa.me/{$defaultPhone}?text={$encodedMsg}"
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
