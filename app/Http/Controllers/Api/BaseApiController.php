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
    protected const AUTH_REGISTRATION_FAILED = 'Registrasi gagal';
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
protected function execute(callable $action, string $sourceName, string $actionType = self::ACTION_GET): JsonResponse
{
    try {
        $result = $action();
        $message = sprintf($this->messageTemplates[$actionType]['success'], $sourceName);
        return $this->successResponse($result, $message);
    } catch (\Exception $e) {
        $this->logError('Execution error', $e);
        
        $message = sprintf($this->messageTemplates[$actionType]['error'], $sourceName);
        return $this->errorResponse($message, $e->getMessage(), 500);
    }
}

/**
 * untuk rute api yang memerlukan autentikasi token (auth:api)
 */
protected function executeWithAuthentication(callable $action, string $sourceName, string $actionType = self::ACTION_GET): JsonResponse
{
    try {
        if (!JWTAuth::getToken()) {
            return $this->errorResponse(self::AUTH_TOKEN_NOT_FOUND, null, 401);
        }

        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return $this->errorResponse(self::AUTH_USER_NOT_FOUND, null, 401);
        }
        
        return $this->execute(
            function() use ($action, $user) {
                return $action($user);
            },
            $sourceName,
            $actionType
        );
        
    } catch (TokenExpiredException $e) {
        return $this->errorResponse(self::AUTH_TOKEN_EXPIRED, null, 401);
    } catch (TokenInvalidException $e) {
        return $this->errorResponse(self::AUTH_TOKEN_INVALID, null, 401);
    } catch (JWTException $e) {
        $this->logError('JWT Error', $e);
        return $this->errorResponse(self::AUTH_TOKEN_INVALID, $e->getMessage(), 401);
    } catch (\Exception $e) {
        $this->logError('Authentication action error', $e);
        $message = sprintf($this->messageTemplates[$actionType]['error'], $sourceName);
        return $this->errorResponse($message, $e->getMessage(), 500);
    }
}

/**
 * for api routes requiring validation or validating input (login & register)
 */
protected function executeWithValidation(callable $validatorAction, callable $successAction, string $actionType): JsonResponse
{
    try {
        $validator = $validatorAction();
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => self::VALIDATION_FAILED,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $result = $successAction();
        $message = $this->messageTemplates[$actionType]['success'];
        
        return $this->successResponse($result, $message);
    } catch (\Exception $e) {
        $this->logError('Action validation error', $e);
        return $this->errorResponse(self::SERVER_ERROR, $e->getMessage(), 500);
    }
}

/**
 * Return success response in JSON format.
 */
protected function successResponse($data, string $message = 'Operation successful', int $statusCode = 200): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

/**
 * Return error response in JSON format.
 */
protected function errorResponse(string $message = 'An error occurred', $error = null, int $statusCode = 500): JsonResponse
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
 * Log error to system log.
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