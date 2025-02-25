<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Login user dan generate JWT token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only(['username', 'password']), [
            'username' => 'required|string',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserModel::where('nik_pengguna', $request->username)
                ->orWhere('email_pengguna', $request->username)
                ->orWhere('no_hp_pengguna', $request->username)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kredensial tidak valid'
                ], 401);
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
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
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Register user baru
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:4|max:20|unique:m_user,username',
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|digits_between:10,15',
            'level_id' => 'required|exists:m_level,level_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

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

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'id' => $user->user_id,
                    'username' => $user->username,
                    'name' => $user->nama_pengguna,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Logout user dan invalidate token
     */
    // public function logout(): JsonResponse
    // {
    //     try {
    //         JWTAuth::invalidate(JWTAuth::getToken());
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Logout berhasil'
    //         ]);
    //     } catch (JWTException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal melakukan logout'
    //         ], 500);
    //     }
    // }
    public function logout(): JsonResponse
{
    try {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token); // Hapus token dari daftar valid

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil, token telah dihapus'
        ]);
    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal melakukan logout'
        ], 500);
    }
}


    /**
     * Get data user yang sedang login
     */
    public function getUser(): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->user_id,
                    'name' => $user->nama_pengguna,
                    'email' => $user->email_pengguna,
                    'phone' => $user->no_hp_pengguna,
                    'job' => $user->pekerjaan_pengguna,
                    'nik' => $user->nik_pengguna,
                    'nik_file' => $user->upload_nik_pengguna,
                    'alias' => $this->generateAlias($user->nama_pengguna),
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }
    }

    /**
     * Generate alias dari nama pengguna
     */
    private function generateAlias(string $name): string
    {
        $words = explode(' ', $name);
        $alias = '';

        foreach ($words as $word) {
            if (strlen($alias . ' ' . $word) > 15) {
                $alias .= ' ' . strtoupper(substr($word, 0, 1)) . '.';
                break;
            }
            $alias .= ($alias === '' ? '' : ' ') . $word;
        }

        return trim($alias);
    }
}