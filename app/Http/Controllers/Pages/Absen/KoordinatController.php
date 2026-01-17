<?php

namespace App\Http\Controllers\Pages\Absen;

use App\Http\Controllers\Controller;
use App\Models\Koordinat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KoordinatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $sort   = $request->sort ?? 10;

        $coordinat = Koordinat::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%");
        })
            ->orderBy('id', 'DESC')
            ->paginate($sort)
            ->appends($request->query());

        return view("pages.koordinat.index", compact("coordinat"));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required|string",
            "lat"  => "required|string",
            "lng"  => "required|string",
            "radius" => "required|string"
        ]);

        if ($validation->fails()) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "errors" => $validation->errors()
            ]);
        }

        $post = $request->all();

        Koordinat::create($post);

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil menambahkan data."
        ]);
    }

    public function show($id)
    {
        $coordinat = Koordinat::find($id);

        if (!$coordinat) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "data not found."
            ]);
        }

        return response()->json([
            "code" => 200,
            "status" => "success",
            "data" => $coordinat
        ]);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required|string",
            "lat"  => "required|string",
            "lng"  => "required|string",
            "radius" => "required|string"
        ]);

        if ($validation->fails()) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "errors" => $validation->errors()
            ]);
        }

        $put = $request->all();

        $koordinat = Koordinat::find($id);

        $koordinat->update($put);

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil mengubah data data."
        ]);
    }

    public function destroy($id)
    {
        $coordinat = Koordinat::find($id);

        if (!$coordinat) {
            return response()->json([
                "code" => 400,
                "status" => "error",
                "message" => "data not found."
            ]);
        }

        $coordinat->delete();

        return response()->json([
            "code" => 200,
            "status" => "success",
            "message" => "Berhasil menghapus data data."
        ]);
    }
}
