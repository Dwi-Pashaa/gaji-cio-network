<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeCashAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $type = CashAdvanceType::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        })
            ->orderBy('id', 'DESC')
            ->paginate($sort);

        return view("pages.type.index", compact("type"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required",
            "amount" => "required",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'errors' => $validation->errors()
            ]);
        }

        $post = $request->only('name');

        $amount = preg_replace('/[^0-9]/', '', $request->amount);
        $post['amount'] = $amount;

        CashAdvanceType::create($post);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menyimpan data.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $type = CashAdvanceType::find($id);

        if (!$type) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        return response()->json(['code' => 200, 'status' => 'success', 'data' => $type]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $type = CashAdvanceType::find($id);

        $validation = Validator::make($request->all(), [
            "name" => "required",
            "amount" => "required",
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Opps ada yang belum di isi.',
                'errors' => $validation->errors()
            ]);
        }

        $put = $request->only('name');

        $amount = preg_replace('/[^0-9]/', '', $request->amount);
        $put['amount'] = $amount;

        $type->update($put);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil mengubah data.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $type = CashAdvanceType::find($id);

        if (!$type) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        $type->delete();

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menghapus data.']);
    }
}
