<?php
namespace App\Http\Controllers\Api;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuthController extends BaseApiController
{
    /**
     * Login user dan generate JWT token
     */
    public function login(Request $request)
    {
        return $this->eksekusi(
            function() use ($request) {
                $loginResult = UserModel::loginProcess(
                    $request->username, 
                    $request->password
                );
    
                if (!$loginResult) {
                    return $this->responKesalahan(self::AUTH_INVALID_CREDENTIALS, null, 401);
                }
    
                return $loginResult;
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

            return $this->responSukses(null, self::AUTH_LOGOUT_SUCCESS);
        } catch (JWTException $e) {
            return $this->responKesalahan(self::AUTH_LOGOUT_FAILED, $e->getMessage(), 500);
        }
    }

    /**
     * Get data user yang sedang login
     */
    public function getUser()
    {
        return $this->eksekusiDenganOtentikasi(
            function($user) {
                return $user->getUserData();
            },
            'user'
        );
    }
}