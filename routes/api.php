<?php

use Spatie\FlareClient\Api;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\Auth\ApiFooterController;
use App\Http\Controllers\Api\public\ApiMenuController;
use App\Http\Controllers\Api\Public\ApiBeritaController;
use App\Http\Controllers\Api\Public\ApiAksesCepatController;
use App\Http\Controllers\Api\Auth\BeritaPengumumanController;
use App\Http\Controllers\Api\Public\ApiMediaDinamisController;
use App\Http\Controllers\Api\Public\ApiBeritaLandingPageController;
use App\Http\Controllers\Api\Public\ApiPengumumanLandigPageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    // Public routes (tidak perlu autentikasi)
    Route::post('login', [ApiAuthController::class, 'login']);
    // Route::post('register', [ApiAuthController::class, 'register']);
    
    // Protected routes (perlu autentikasi)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('user', [ApiAuthController::class, 'getData']);
        Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
        Route::get('footerData', [ApiFooterController::class, 'getDataFooter']);
        
    });
});

// route publik
Route::group(['prefix' => 'public'], function () {
    Route::get('getDataMenu', [ApiMenuController::class, 'getDataMenu']);
    Route::get('getDataAksesCepat',[ApiAksesCepatController::class,'getDataAksesCepat']);
    Route::get('getDataPengumumanLandingPage',[ApiPengumumanLandigPageController::class,'getDataPengumumanLandingPage']);
    Route::get('getDataBeritaLandingPage',[ApiBeritaLandingPageController::class,'getDataBeritaLandingPage']);
    Route::get('getDataBerita',[ApiBeritaController::class,'getDataBerita']);
    Route::get('getDataDetailBerita/{slug}', [ApiBeritaController::class, 'getDataDetailBerita']);
    
    Route::get('getDataHeroSection',[ApiMediaDinamisController::class,'getDataHeroSection']);
    Route::get('getDataDokumentasi',[ApiMediaDinamisController::class,'getDataDokumentasi']);
    Route::get('getDataMediaInformasiPublik',[ApiMediaDinamisController::class,'getDataMediaInformasiPublik']);
});