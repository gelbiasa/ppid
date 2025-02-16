<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardMPUController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\AdminWeb\MenuUtamaController;
use App\Http\Controllers\DashboardRespondenController;
use App\Http\Controllers\DashboardVerifikatorController;

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

Route::pattern('id', '[0-9]+'); // Artinya: Ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);

// Group route yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboardAdmin', [DashboardAdminController::class, 'index'])->middleware('authorize:ADM');
    Route::get('/dashboardMPU', [DashboardMPUController::class, 'index'])->middleware('authorize:MPU');
    Route::get('/dashboardVFR', [DashboardVerifikatorController::class, 'index'])->middleware('authorize:VFR');
    Route::get('/dashboardResponden', [DashboardRespondenController::class, 'index'])->middleware('authorize:RPN');

    Route::get('/session', [AuthController::class, 'getSessionData']);

    Route::group(['prefix' => 'adminweb/menu-utama', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [MenuUtamaController::class, 'index']);
        Route::post('/list', [MenuUtamaController::class, 'list']);
        Route::get('/create_ajax', [MenuUtamaController::class, 'create_ajax']);
        Route::post('/ajax', [MenuUtamaController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [MenuUtamaController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [MenuUtamaController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [MenuUtamaController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [MenuUtamaController::class, 'delete_ajax']);
    });
    
});