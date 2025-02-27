<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class BaseApiController extends Controller
{
    // Konstanta untuk berbagai jenis aksi API
    protected const ACTION_GET = 'get';
    protected const ACTION_CREATE = 'create';
    protected const ACTION_UPDATE = 'update';
    protected const ACTION_DELETE = 'delete';
    
    // Konstanta untuk pesan error autentikasi
    protected const AUTH_TOKEN_NOT_FOUND = 'Token tidak ditemukan';
    protected const AUTH_USER_NOT_FOUND = 'User tidak ditemukan';
    protected const AUTH_TOKEN_EXPIRED = 'Token telah kadaluarsa';
    protected const AUTH_TOKEN_INVALID = 'Token tidak valid';
   
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
    ];

    /**
     * Mengeksekusi aksi dan memberikan respons yang terstandarisasi.
     * @param callable $action Fungsi yang akan dieksekusi
     * @param string $resourceName Nama resource (contoh: 'menu', 'user')
     * @param string $actionType Jenis aksi (get, create, update, delete, dll)
     * @return JsonResponse
     */
    protected function execute(callable $action, string $resourceName, string $actionType = self::ACTION_GET): JsonResponse
    {
        try {
            // Eksekusi aksi yang diberikan
            $result = $action();
            // Ambil pesan sukses berdasarkan jenis aksi
            $message = sprintf($this->messageTemplates[$actionType]['success'], $resourceName);
            return $this->successResponse($result, $message);
        } catch (\Exception $e) {
            // Log error untuk debugging
            $this->logError('Execute error', $e);
            
            // Ambil pesan error berdasarkan jenis aksi
            $message = sprintf($this->messageTemplates[$actionType]['error'], $resourceName);
            return $this->errorResponse($message, $e->getMessage(), 500);
        }
    }
    
    /**
     * Mengeksekusi aksi yang membutuhkan autentikasi JWT dan memberikan respons terstandarisasi.
     * @param callable $action Fungsi yang akan dieksekusi jika autentikasi berhasil
     * @param string $resourceName Nama resource (contoh: 'menu', 'user')
     * @param string $actionType Jenis aksi (get, create, update, delete, dll)
     * @return JsonResponse
     */
    protected function executeWithAuth(callable $action, string $resourceName, string $actionType = self::ACTION_GET): JsonResponse
    {
        try {
            // Cek keberadaan token
            if (!JWTAuth::getToken()) {
                return $this->errorResponse(self::AUTH_TOKEN_NOT_FOUND, null, 401);
            }

            // Autentikasi user dari token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->errorResponse(self::AUTH_USER_NOT_FOUND, null, 401);
            }
            
            // Eksekusi aksi dengan menyertakan user
            return $this->execute(
                function() use ($action, $user) {
                    return $action($user);
                },
                $resourceName,
                $actionType
            );
            
        } catch (TokenExpiredException $e) {
            return $this->errorResponse(self::AUTH_TOKEN_EXPIRED, null, 401);
        } catch (TokenInvalidException $e) {
            return $this->errorResponse(self::AUTH_TOKEN_INVALID, null, 401);
        } catch (JWTException $e) {
            $this->logError('JWT error', $e);
            return $this->errorResponse(self::AUTH_TOKEN_INVALID, $e->getMessage(), 401);
        } catch (\Exception $e) {
            $this->logError('Auth action error', $e);
            $message = sprintf($this->messageTemplates[$actionType]['error'], $resourceName);
            return $this->errorResponse($message, $e->getMessage(), 500);
        }
    }
    
    /**
     * Mengembalikan respons sukses dalam format JSON.
     *
     * @param mixed $data Data yang akan dikirim dalam respons
     * @param string $message Pesan sukses (default: 'Operasi berhasil')
     * @param int $statusCode HTTP status code (default: 200)
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = 'Operasi berhasil', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    /**
     * Mengembalikan respons error dalam format JSON.
     * @param string $message Pesan error (default: 'Terjadi kesalahan')
     * @param mixed $error Detail kesalahan (opsional)
     * @param int $statusCode HTTP status code (default: 500)
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Terjadi kesalahan', $error = null, int $statusCode = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($error !== null && config('app.debug')) {
            $response['error'] = $error;
        }
        
        return response()->json($response, $statusCode);
    }
    
    /**
     * Mencatat error ke log sistem.
     * @param string $context Konteks error
     * @param \Exception $exception Objek exception
     * @return void
     */
    protected function logError(string $context, \Exception $exception): void
    {
        if (class_exists('Log')) {
            Log::error($context, [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
        }
    }
}