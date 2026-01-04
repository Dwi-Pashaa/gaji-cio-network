<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        ], function ($view) {

            $isAbsenOn = DB::table('setting')
                ->where('key', 'fiture_absen')
                ->value('value') === 'active';

            $view->with('isAbsenOn', $isAbsenOn);
        });
    }
}
