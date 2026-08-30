<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceType;
use App\Models\Expenditure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenditureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $expenditure = Expenditure::with(['type'])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%$search%")
                    ->orWhereHas('type', function ($query, $search) {
                        $query->where('name', 'like', "%$search%");
                    });
            })
            ->orderBy('id', 'DESC')
            ->paginate($sort);

        $type = CashAdvanceType::all();

        return view("pages.expenditure.index", compact("expenditure", "type"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\FinanceApiService $financeApi)
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

        $amount = (float) preg_replace('/[^0-9]/', '', $request->amount);

        // Cek saldo website dari API Finance jika sudah terkonfigurasi
        if ($financeApi->isConfigured()) {
            $balanceResult = $financeApi->getBalance();
            if (!$balanceResult['success']) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Gagal memeriksa saldo website: ' . $balanceResult['message'],
                ]);
            }

            if ($balanceResult['balance'] < $amount) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Saldo website tidak mencukupi untuk pengeluaran ini. Saldo saat ini: Rp ' . number_format($balanceResult['balance'], 0, ',', '.') . ', dibutuhkan: Rp ' . number_format($amount, 0, ',', '.') . '.',
                ]);
            }
        }

        $post = $request->all();
        $post['amount'] = $amount;
        $post['date'] = Carbon::now();

        $expenditure = Expenditure::create($post);

        $type = CashAdvanceType::find($request->type_id);
        if ($type) {
            $type->amount = max(0, $type->amount - $amount);
            $type->save();
        }

        // Potong saldo website langsung via Finance API
        if ($financeApi->isConfigured()) {
            $financeApi->deductBalance(
                amount:       $amount,
                referenceId:  'EXP-' . $expenditure->id,
                description:  'Pengeluaran: ' . $request->title,
                category:     'pengeluaran',
                note:         $request->note ?? 'Pengeluaran kantor'
            );
        }

        $defaultPhone = "6285324780031";
        $message = "Konfirmasi Pengeluaran\n\n"
            . "Judul Pengeluaran: {$request->title}\n"
            . "Tipe Pengeluaran: " . ($type->name ?? '-') . "\n"
            . "Jumlah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Tanggal Pengeluaran: " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $encodedMsg = rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil menyimpan data pengeluaran.',
            'wa_link' => "https://wa.me/{$defaultPhone}?text={$encodedMsg}"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expenditure = Expenditure::find($id);

        if (!$expenditure) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        return response()->json(['code' => 200, 'status' => 'success', 'data' => $expenditure]);
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

        $expenditure = Expenditure::find($id);

        if (!$expenditure) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $oldAmount = $expenditure->amount;
        $oldTypeId = $expenditure->type_id;

        $amount = preg_replace('/[^0-9]/', '', $request->amount);
        $expenditure->update([
            "title" => $request->title,
            "amount" => $amount,
            "type_id" => $request->type_id,
        ]);

        if ($oldTypeId != $request->type_id) {
            $oldType = CashAdvanceType::find($oldTypeId);
            if ($oldType) {
                $oldType->amount += $oldAmount;
                $oldType->save();
            }
        }

        $type = CashAdvanceType::find($request->type_id);
        if ($type) {
            if ($oldTypeId == $request->type_id) {
                $selisih = $amount - $oldAmount;
                $type->amount = max(0, $type->amount - $selisih);
            } else {
                $type->amount = max(0, $type->amount - $amount);
            }
            $type->save();
        }

        $defaultPhone = "6285324780031";
        $message = "Konfirmasi Pengeluaran\n\n"
            . "Judul Pengeluaran: {$request->title}\n"
            . "Tipe Pengeluaran: {$type->name}\n"
            . "Jumlah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "Tanggal Pengeluaran: " . Carbon::now()->translatedFormat('l, d F Y - H:i');

        $encodedMsg = rawurlencode($message);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil memperbarui data.',
            'wa_link' => "https://wa.me/{$defaultPhone}?text={$encodedMsg}"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expenditure = Expenditure::find($id);

        if (!$expenditure) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        if ($expenditure->type_id) {
            $type = CashAdvanceType::find($expenditure->type_id);
            if ($type) {
                $type->amount += $expenditure->amount;
                $type->save();
            }
        }

        $expenditure->delete();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Berhasil menghapus data.',
        ]);
    }
}
