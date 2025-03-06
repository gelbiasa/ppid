<?php
namespace App\Http\Controllers\Api;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends BaseApiController
{
    public function login(Request $request)
    {
        return $this->eksekusi(
            function() use ($request) {
                // Calling the processLogin method from UserModel to handle login
                $loginResult = UserModel::prosesLogin($request);

                // Checking if login was successful
                if (!$loginResult['success']) {
                    return $this->responKesalahan(self::AUTH_INVALID_CREDENTIALS, $loginResult['message'], 401);
                }

                // Generate token
                $user = UserModel::where('nik_pengguna', $request->username)
                    ->orWhere('email_pengguna', $request->username)
                    ->orWhere('no_hp_pengguna', $request->username)
                    ->first();

                // Generate token
                $token = JWTAuth::fromUser($user);

                // Jika login berhasil, kembalikan token dan informasi
                return response()->json([
                    'success' => true,
                    'message' => $loginResult['message'],
                    'redirect' => $loginResult['redirect'],
                    'token' => $token,
                    'user' => UserModel::getDataUser($user)
                ]);
            },
            'login',
            self::ACTION_LOGIN
        );
    }

    /**
     * Logout user dan invalidate token
     */
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);

            // Successfully invalidated the token
            return $this->responSukses(null, self::AUTH_LOGOUT_SUCCESS);
        } catch (JWTException $e) {
            // If error occurs during token invalidation
            return $this->responKesalahan(self::AUTH_LOGOUT_FAILED, $e->getMessage(), 500);
        }
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        return $this->eksekusi(
            function() use ($request) {
                // Calling the processRegister method from UserModel to handle user registration
                $registerResult = UserModel::prosesRegister($request);

                // If registration is successful, return the success message
                if (!$registerResult['success']) {
                    return $this->responKesalahan(self::AUTH_REGISTRATION_FAILED, $registerResult['message'], 400);
                }

                // Successful registration response
                return response()->json([
                    'success' => true,
                    'message' => $registerResult['message'],
                    'redirect' => $registerResult['redirect']  // Optional: include a redirect URL
                ]);
            },
            'register',
            self::ACTION_REGISTER
        );
    }

    /**
     * Get data user yang sedang login
     */
    public function getData()
    {
        return $this->eksekusiDenganOtentikasi(
            function($user) {
                // Return user data for the logged-in user
                return UserModel::getDataUser($user);
            },
            'user'
        );
    }
}