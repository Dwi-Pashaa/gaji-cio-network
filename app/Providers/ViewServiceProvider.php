<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer([
            'pages.dashboard.index',
            'pages.absen.*',
            'pages.user.set-absensi'
        ], function ($view) {

            // =========================
            // AMBIL USER ID
            // =========================
            $userId = null;

            // jika halaman set absensi (admin edit user)
            if (Request::routeIs('user.setting') || Request::route('id')) {
                $userId = Request::route('id');
            }

            // fallback ke user login
            if (! $userId && Auth::check()) {
                $userId = Auth::user()->id;
            }

            // default false jika tidak ada user
            $isAbsenOn = false;

            if ($userId) {
                $isAbsenOn = DB::table('setting')
                    ->where('key', 'fiture_absen')
                    ->where('user_id', $userId)
                    ->value('value') === 'active';
            }

            $view->with('isAbsenOn', $isAbsenOn);
        });
    }
}
