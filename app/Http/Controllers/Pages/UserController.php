<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserWorkDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->orderBy('id', 'DESC')
            ->paginate($sort)
            ->appends($request->query());

        return view("pages.user.index", compact("users"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Role::all();
        return view("pages.user.create", compact("role"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "username" => "required|unique:users,username",
            "name" => "required|string",
            "email" => "required|unique:users,email",
            "role" => "required",
            "phone" => "required",
            "password" => "required|string|min:8|confirmed",
            "password_confirmation" => "required|string"
        ]);

        $post = $request->except('password_confirmation', 'role');

        $user = User::create($post);
        $post['password'] = Hash::make($request->password);
        $user->assignRole($request->role);

        return redirect()->route('user.index')->with('success', 'Berhasil menambahkan user baru.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $role = Role::all();
        return view("pages.user.edit", compact("user", "role"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        $request->validate([
            "username" => "required|unique:users,username," . $user->id,
            "name" => "required|string",
            "email" => "required|unique:users,email," . $user->id,
            "role" => "required",
            "phone" => "required",
            "password" => "nullable|string|min:8|confirmed",
        ]);

        $updateData = $request->except('password', 'password_confirmation', 'role');

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $user->syncRoles([$request->role]);

        return redirect()->route('user.index')->with('success', 'Berhasil memperbarui user.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['code' => 400, 'status' => 'error', 'message' => 'Data Not Found.']);
        }

        $user->delete();

        return response()->json(['code' => 200, 'status' => 'success', 'message' => 'Berhasil menghapus data.']);
    }

    public function workday(string $id)
    {
        $user = User::with(['workday'])->find($id);

        return view("pages.user.workday", compact("user"));
    }

    public function saveWorkDay(Request $request, $id)
    {
        $request->validate([
            'weekday' => 'required|array',
            'weekday.*' => 'in:0,1,2,3,4,5,6',
        ]);

        DB::transaction(function () use ($request, $id) {

            UserWorkDay::where('user_id', $id)->delete();

            $data = collect($request->weekday)->map(function ($day) use ($id) {
                return [
                    'user_id'    => $id,
                    'weekday'    => $day,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            UserWorkday::insert($data);
        });

        return back()->with('success', 'Hari kerja berhasil disimpan.');
    }
}
