<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends BaseApiController
{
    /**
     * Login user dan generate JWT token
     */
    public function login(Request $request): JsonResponse
    {
        return $this->executeWithValidation(
            function() use ($request) {
                return Validator::make($request->only(['username', 'password']), [
                    'username' => 'required|string',
                    'password' => 'required|string|min:5',
                ]);
            },
            function() use ($request) {
                $user = UserModel::where('nik_pengguna', $request->username)
                    ->orWhere('email_pengguna', $request->username)
                    ->orWhere('no_hp_pengguna', $request->username)
                    ->first();

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return $this->errorResponse(self::AUTH_INVALID_CREDENTIALS, null, 401);
                }

                $token = JWTAuth::fromUser($user);

                return [
                    'user' => [
                        'id' => $user->user_id,
                        'name' => $user->nama_pengguna,
                        'email' => $user->email_pengguna,
                        'phone' => $user->no_hp_pengguna,
                        'job' => $user->pekerjaan_pengguna,
                        'nik' => $user->nik_pengguna,
                        'nik_file' => $user->upload_nik_pengguna,
                        'alias' => $this->generateAlias($user->nama_pengguna),
                    ],
                    'token' => $token
                ];
            },
            self::ACTION_LOGIN
        );
    }

    /**
     * Register user baru
     */
    public function register(Request $request): JsonResponse
    {
        return $this->executeWithValidation(
            function() use ($request) {
                return Validator::make($request->all(), [
                    'username' => 'required|string|min:4|max:20|unique:m_user,username',
                    'name' => 'required|string|min:2|max:50',
                    'email' => 'required|email|unique:m_user,email',
                    'password' => 'required|string|min:6|confirmed',
                    'phone' => 'required|string|digits_between:10,15',
                    'level_id' => 'required|exists:m_level,level_id',
                ]);
            },
            function() use ($request) {
                DB::beginTransaction();
                try {
                    $user = UserModel::create([
                        'username' => $request->username,
                        'nama_pengguna' => $request->name,
                        'email_pengguna' => $request->email,
                        'password' => Hash::make($request->password),
                        'no_hp_pengguna' => $request->phone,
                        'level_id' => $request->level_id,
                    ]);

                    DB::commit();

                    return [
                        'id' => $user->user_id,
                        'username' => $user->username,
                        'name' => $user->nama_pengguna,
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            },
            self::ACTION_REGISTER
        );
    }

    /**
     * Logout user dan invalidate token
     */
    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token); // Hapus token dari daftar valid

            return $this->successResponse(null, self::AUTH_LOGOUT_SUCCESS);
        } catch (JWTException $e) {
            return $this->errorResponse(self::AUTH_LOGOUT_FAILED, $e->getMessage(), 500);
        }
    }

    /**
     * Get data user yang sedang login
     */
    public function getUser(): JsonResponse
    {
        return $this->executeWithAuth(
            function($user) {
                return [
                    'id' => $user->user_id,
                    'name' => $user->nama_pengguna,
                    'email' => $user->email_pengguna,
                    'phone' => $user->no_hp_pengguna,
                    'job' => $user->pekerjaan_pengguna,
                    'nik' => $user->nik_pengguna,
                    'nik_file' => $user->upload_nik_pengguna,
                    'alias' => $this->generateAlias($user->nama_pengguna),
                ];
            },
            'user'
        );
    }
}