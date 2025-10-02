<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $allowance = Allowance::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        })
            ->orderBy('id', 'DESC')
            ->paginate($sort);

        return view("pages.allowance.index", compact("allowance"));
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

        Allowance::create($post);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menyimpan data.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $allowance = Allowance::find($id);

        if (!$allowance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        return response()->json(['code' => 200, 'status' => 'success', 'data' => $allowance]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $allowance = Allowance::find($id);

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

        $allowance->update($put);

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil mengubah data.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $allowance = Allowance::find($id);

        if (!$allowance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'Data Not Found.',
            ]);
        }

        $allowance->delete();

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menghapus data.']);
    }
}
