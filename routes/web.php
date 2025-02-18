<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardMPUController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardRespondenController;
use App\Http\Controllers\DashboardVerifikatorController;
use App\Http\Controllers\AdminWeb\MenuManagementController;
use App\Http\Controllers\AdminWeb\SubMenu\SubMenuController;
use App\Http\Controllers\AdminWeb\menuUtama\MenuUtamaController;
use App\Http\Controllers\SistemInformasi\EForm\PermohonanInformasiController;

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

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/update_pengguna/{id}', [ProfileController::class, 'update_pengguna']);
        Route::put('/update_password/{id}', [ProfileController::class, 'update_password']);
    });

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

    Route::group(['prefix' => 'adminweb/menu-management', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [MenuManagementController::class, 'index']);
        Route::get('/menu-item', [MenuManagementController::class, 'menu-item']);
        Route::post('/list', [MenuManagementController::class, 'list']);
        Route::post('/store', [MenuManagementController::class, 'store']);
        Route::get('/{id}/edit', [MenuManagementController::class, 'edit']);
        Route::put('/{id}/update', [MenuManagementController::class, 'update']);
        Route::delete('/{id}/delete', [MenuManagementController::class, 'delete']);
        Route::post('/reorder', [MenuManagementController::class, 'reorder']); // New route for drag-drop reordering
       
    });
    Route::group(['prefix' => 'adminweb/submenu', 'middleware' => 'authorize:ADM'], function () {
        Route::get('/', [SubMenuController::class, 'index']);
        Route::post('/list', [SubMenuController::class, 'list']);
        Route::get('/create_ajax', [SubMenuController::class, 'create_ajax']);
        Route::post('/ajax', [SubMenuController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [SubMenuController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [SubMenuController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [SubMenuController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [SubMenuController::class, 'delete_ajax']);
    });

    Route::group(['prefix' => 'SistemInformasi/EForm/PermohonanInformasi', 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [PermohonanInformasiController::class, 'index']);
        Route::get('/formPermohonanInformasi', [PermohonanInformasiController::class, 'formPermohonanInformasi']);
        Route::post('/storePermohonanInformasi', [PermohonanInformasiController::class, 'storePermohonanInformasi']);
    });
});