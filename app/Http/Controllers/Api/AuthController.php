<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\UserModel;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    /**
     * Login user dan generate JWT token
     */
    public function login(Request $request)
    {
        return $this->execute(function() use ($request) {
            return UserModel::loginUser($request);
        }, 'authentication', self::ACTION_GET);
    }

    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        return $this->execute(function() use ($request) {
            return UserModel::registerUser($request);
        }, 'user', self::ACTION_CREATE);
    }

    /**
     * Logout user dan invalidate token
     */
    public function logout()
    {
        return $this->execute(function() {
            return UserModel::logoutUser();
        }, 'session', self::ACTION_DELETE);
    }

    /**
     * Get data user yang sedang login
     */
    public function getUser()
    {
        return $this->executeWithAuth(function($user) {
            return UserModel::getUserProfile($user);
        }, 'profile', self::ACTION_GET);
    }
}