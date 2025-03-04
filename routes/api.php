<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthMenuController;
use App\Http\Controllers\Api\Auth\BeritaPengumumanController;
use App\Http\Controllers\Api\Public\PublicMenuController;
use Spatie\FlareClient\Api;

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
        Route::get('user', [ApiAuthController::class, 'getUser']);
        Route::get('menus', [AuthMenuController::class, 'getAuthMenus']);
        Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
    });
});

// route publik
Route::group(['prefix' => 'public'], function () {
    Route::get('menus', [PublicMenuController::class, 'getPublicMenus']);
});