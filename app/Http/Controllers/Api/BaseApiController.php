<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\TraitsController;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class BaseApiController extends Controller
{
    use TraitsController;
    // Konstanta untuk berbagai jenis aksi API
    protected const ACTION_GET = 'get';
    protected const ACTION_CREATE = 'create';
    protected const ACTION_UPDATE = 'update';
    protected const ACTION_DELETE = 'delete';
    protected const ACTION_LOGIN = 'login';
    protected const ACTION_REGISTER = 'register';
    protected const ACTION_LOGOUT = 'logout';
    
    // Konstanta untuk pesan error autentikasi
    protected const AUTH_TOKEN_NOT_FOUND = 'Token tidak ditemukan';
    protected const AUTH_USER_NOT_FOUND = 'User tidak ditemukan';
    protected const AUTH_TOKEN_EXPIRED = 'Token telah kadaluarsa';
    protected const AUTH_TOKEN_INVALID = 'Token tidak valid';
    protected const AUTH_INVALID_CREDENTIALS = 'Login gagal. Pastikan username dan password yang Anda masukkan benar.';
    protected const AUTH_LOGIN_SUCCESS = 'Login berhasil';
    protected const AUTH_REGISTER_SUCCESS = 'Registrasi berhasil';
    protected const AUTH_LOGOUT_SUCCESS = 'Logout berhasil, token telah dihapus';
    protected const AUTH_LOGOUT_FAILED = 'Gagal melakukan logout';
    protected const VALIDATION_FAILED = 'Validasi gagal';
    protected const SERVER_ERROR = 'Terjadi kesalahan pada server';
   
    // Template pesan untuk setiap aksi
    protected $messageTemplates = [
        self::ACTION_GET => [
            'success' => 'Data %s berhasil diambil.',
            'error' => 'Gagal mengambil data %s. Silakan coba lagi.'
        ],
        self::ACTION_CREATE => [
            'success' => 'Data %s berhasil dibuat.',
            'error' => 'Gagal membuat data %s. Silakan coba lagi.'
        ],
        self::ACTION_UPDATE => [
            'success' => 'Data %s berhasil diperbarui.',
            'error' => 'Gagal memperbarui data %s. Silakan coba lagi.'
        ],
        self::ACTION_DELETE => [
            'success' => 'Data %s berhasil dihapus.',
            'error' => 'Gagal menghapus data %s. Silakan coba lagi.'
        ],
        self::ACTION_LOGIN => [
            'success' => self::AUTH_LOGIN_SUCCESS,
            'error' => self::AUTH_INVALID_CREDENTIALS
        ],
        self::ACTION_REGISTER => [
            'success' => self::AUTH_REGISTER_SUCCESS,
            'error' => self::VALIDATION_FAILED
        ],
        self::ACTION_LOGOUT => [
            'success' => self::AUTH_LOGOUT_SUCCESS,
            'error' => self::AUTH_LOGOUT_FAILED
        ],
    ];

    /**
     * Mengeksekusi aksi dan memberikan respons yang terstandarisasi.
     * @param callable $action Fungsi yang akan dieksekusi
     * @param string $resourceName Nama resource (contoh: 'menu', 'user')
     * @param string $actionType Jenis aksi (get, create, update, delete, dll)
     * @return JsonResponse
     */
    // untuk Akses publik tanpa autentikasi
     protected function eksekusi(callable $aksi, string $namaSumber, string $jenisAksi = self::ACTION_GET): JsonResponse
    {
        try {
            $hasil = $aksi();
            $pesan = sprintf($this->messageTemplates[$jenisAksi]['success'], $namaSumber);
            return $this->responSukses($hasil, $pesan);
        } catch (\Exception $e) {
            $this->catatKesalahan('Kesalahan eksekusi', $e);
            
            $pesan = sprintf($this->messageTemplates[$jenisAksi]['error'], $namaSumber);
            return $this->responKesalahan($pesan, $e->getMessage(), 500);
        }
    }
    
    /**
     * untuk route api perlu token (auth:api)  terautentikasi
     * 
     */
    protected function eksekusiDenganOtentikasi(callable $aksi, string $namaSumber, string $jenisAksi = self::ACTION_GET): JsonResponse
    {
        try {
            if (!JWTAuth::getToken()) {
                return $this->responKesalahan(self::AUTH_TOKEN_NOT_FOUND, null, 401);
            }

            $pengguna = JWTAuth::parseToken()->authenticate();
            if (!$pengguna) {
                return $this->responKesalahan(self::AUTH_USER_NOT_FOUND, null, 401);
            }
            
            return $this->eksekusi(
                function() use ($aksi, $pengguna) {
                    return $aksi($pengguna);
                },
                $namaSumber,
                $jenisAksi
            );
            
        } catch (TokenExpiredException $e) {
            return $this->responKesalahan(self::AUTH_TOKEN_EXPIRED, null, 401);
        } catch (TokenInvalidException $e) {
            return $this->responKesalahan(self::AUTH_TOKEN_INVALID, null, 401);
        } catch (JWTException $e) {
            $this->catatKesalahan('Kesalahan JWT', $e);
            return $this->responKesalahan(self::AUTH_TOKEN_INVALID, $e->getMessage(), 401);
        } catch (\Exception $e) {
            $this->catatKesalahan('Kesalahan aksi otentikasi', $e);
            $pesan = sprintf($this->messageTemplates[$jenisAksi]['error'], $namaSumber);
            return $this->responKesalahan($pesan, $e->getMessage(), 500);
        }
    }
    
    /**
     * untuk route api memerlukan validasi atau memvalidasi input (login & register)
     */
    protected function eksekusiDenganValidasi(callable $aksiValidator, callable $aksiSukses, string $jenisAksi): JsonResponse
    {
        try {
            $validator = $aksiValidator();
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => self::VALIDATION_FAILED,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $hasil = $aksiSukses();
            $pesan = $this->messageTemplates[$jenisAksi]['success'];
            
            return $this->responSukses($hasil, $pesan);
        } catch (\Exception $e) {
            $this->catatKesalahan('Kesalahan validasi aksi', $e);
            return $this->responKesalahan(self::SERVER_ERROR, $e->getMessage(), 500);
        }
    }
    
    /**
     * Kembalikan respons sukses dalam format JSON.
     */
    protected function responSukses($data, string $pesan = 'Operasi berhasil', int $kodeStatus = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => $data
        ], $kodeStatus);
    }
    
    /**
     * Kembalikan respons kesalahan dalam format JSON.
     */
    protected function responKesalahan(string $pesan = 'Terjadi kesalahan', $kesalahan = null, int $kodeStatus = 500): JsonResponse
    {
        $respons = [
            'success' => false,
            'message' => $pesan
        ];
        
        if ($kesalahan !== null && config('app.debug')) {
            $respons['error'] = $kesalahan;
        }
        
        return response()->json($respons, $kodeStatus);
    }
    
    /**
     * Catat kesalahan ke log sistem.
     */
    protected function catatKesalahan(string $konteks, \Exception $pengecualian): void
    {
        if (class_exists('Log')) {
            Log::error($konteks, [
                'error' => $pengecualian->getMessage(),
                'trace' => $pengecualian->getTraceAsString()
            ]);
        }
    }
    
}