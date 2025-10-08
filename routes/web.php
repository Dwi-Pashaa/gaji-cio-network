<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pages\AllowanceController;
use App\Http\Controllers\Pages\CashAdvanceController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Pages\SalaryController;
use App\Http\Controllers\Pages\TypeCashAdvanceController;
use App\Http\Controllers\Pages\UserController;
use App\Http\Controllers\Role\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('post.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/print-slip/{month}/{year}', [DashboardController::class, 'slip'])->name('dashboard.print.slip');

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index')->can('lihat level');
        Route::post('/store', [RoleController::class, 'store'])->name('roles.store')->can('tambah level');
        Route::get('/{id}/show', [RoleController::class, 'show'])->name('roles.show')->can('edit level');
        Route::put('/{id}/update', [RoleController::class, 'update'])->name('roles.update')->can('edit level');
        Route::delete('/{id}/destroy', [RoleController::class, 'destroy'])->name('roles.destroy')->can('hapus level');
        Route::get('/{id}/permission', [RoleController::class, 'permission'])->name('roles.permission')->can('edit level');
        Route::put('/{id}/savePermission', [RoleController::class, 'savePermission'])->name('roles.savePermission')->can('edit level');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index')->can('lihat user');
        Route::get('/create', [UserController::class, 'create'])->name('user.create')->can('tambah user');
        Route::post('/store', [UserController::class, 'store'])->name('user.store')->can('tambah user');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('user.edit')->can('edit user');
        Route::put('/{id}/update', [UserController::class, 'update'])->name('user.update')->can('edit user');
        Route::delete('/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy')->can('hapus user');
    });

    Route::prefix('allowance')->group(function () {
        Route::get('/', [AllowanceController::class, 'index'])->name('allowance.index')->can('lihat tunjangan');
        Route::post('/store', [AllowanceController::class, 'store'])->name('allowance.store')->can('tambah tunjangan');
        Route::get('/{id}/show', [AllowanceController::class, 'show'])->name('allowance.show')->can('edit tunjangan');
        Route::put('/{id}/update', [AllowanceController::class, 'update'])->name('allowance.update')->can('edit tunjangan');
        Route::delete('/{id}/destroy', [AllowanceController::class, 'destroy'])->name('allowance.destroy')->can('hapus tunjangan');
    });

    Route::prefix('salary')->group(function () {
        Route::get('/', [SalaryController::class, 'index'])->name('salary.index')->can('lihat gaji karyawan');
        Route::get('/recap', [SalaryController::class, 'recap'])->name('salary.recap')->can('rekap gaji');
        Route::post('/store', [SalaryController::class, 'store'])->name('salary.store')->can('tambah gaji karyawan');
        Route::get('/{id}/show', [SalaryController::class, 'show'])->name('salary.show')->can('edit gaji karyawan');
        Route::get('/{month}/{year}/{id}/detail', [SalaryController::class, 'detail'])->name('salary.detail')->can('edit gaji karyawan');
        Route::put('/{id}/update', [SalaryController::class, 'update'])->name('salary.update')->can('edit gaji karyawan');
        Route::delete('/{id}/destroy', [SalaryController::class, 'destroy'])->name('salary.destroy')->can('hapus gaji karyawan');
    });

    Route::prefix('cash-advance')->group(function () {
        Route::get('/', [CashAdvanceController::class, 'index'])->name('cash.advance.index')->can('lihat kasbon');
        Route::post('/store', [CashAdvanceController::class, 'store'])->name('salary.store')->can('ajukan kasbon');
        Route::get('/{id}/show', [CashAdvanceController::class, 'show'])->name('salary.show')->can('edit kasbon');
        Route::put('/{id}/update', [CashAdvanceController::class, 'update'])->name('salary.update')->can('edit kasbon');
        Route::delete('/{id}/destroy', [CashAdvanceController::class, 'destroy'])->name('salary.destroy')->can('hapus kasbon');

        Route::prefix('approval')->group(function () {
            Route::get('/', [CashAdvanceController::class, 'approval'])->name('cash.advance.approval')->can('approve kasbon');
            Route::put('/{id}/approve', [CashAdvanceController::class, 'approve'])->name('salary.approve')->can('approve kasbon');
            Route::put('/{id}/rejected', [CashAdvanceController::class, 'rejected'])->name('salary.rejected')->can('tolak kasbon');
            Route::post('/updatePhone', [CashAdvanceController::class, 'updatePhone'])->name('salary.updatePhone');
        });

        Route::prefix('type')->group(function () {
            Route::get('/', [TypeCashAdvanceController::class, 'index'])->name('type.cash.advance.index')->can('lihat tipe kasbon');
            Route::post('/store', [TypeCashAdvanceController::class, 'store'])->name('type.cash.advance.store')->can('tambah tipe kasbon');
            Route::get('/{id}/show', [TypeCashAdvanceController::class, 'show'])->name('type.cash.advance.show')->can('edit tipe kasbon');
            Route::put('/{id}/update', [TypeCashAdvanceController::class, 'update'])->name('type.cash.advance.update')->can('edit tipe kasbon');
            Route::delete('/{id}/destroy', [TypeCashAdvanceController::class, 'destroy'])->name('type.cash.advance.destroy')->can('hapus tipe kasbon');
        });
    });
});
