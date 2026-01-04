<?php

namespace App\Http\Controllers\Pages\Absen;

use App\Http\Controllers\Controller;
use App\Models\AttandanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $setting = AttandanceSetting::latest()->first();
        return view("pages.absen.setting.index", compact('setting'));
    }

    /**
     * Store the specified resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_work' => 'required',
            'end_work' => 'required',
            'alpha' => 'required',
            'cuti' => 'required',
            'telat' => 'required',
        ]);

        $post = $request->all();

        $alpha = preg_replace('/[^0-9]/', '', $request->alpha);
        $cuti = preg_replace('/[^0-9]/', '', $request->cuti);
        $telat = preg_replace('/[^0-9]/', '', $request->telat);

        $post['alpha'] = $alpha;
        $post['cuti'] = $cuti;
        $post['telat'] = $telat;

        AttandanceSetting::updateOrCreate(
            ['id' => 1],
            $post
        );

        return redirect()->back()->with('success', 'Berhasil menyimpan pengaturan absen.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function toggleActiveAbsen(Request $request)
    {
        DB::table('setting')
            ->updateOrInsert(
                ['key' => 'fiture_absen'],
                [
                    'value' => $request->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

        return back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}
