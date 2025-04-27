<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\HakAksesModel;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use TraitsController;

    public function login()
    {
        if (Auth::check()) {
            // Arahkan sesuai level pengguna yang login
            $levelCode = Auth::user()->level->hak_akses_kode;
            return redirect('/dashboard' . $levelCode);
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        try {
            $result = UserModel::prosesLogin($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }

            if ($result['success']) {
                // Arahkan sesuai level pengguna yang login
                return $this->redirectSuccess($result['redirect'], $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat login: ' . $e->getMessage()
                ], 500);
            }

            return $this->redirectException($e, 'Terjadi kesalahan saat login');
        }
    }

    public function getData()
    {
        return response()->json(UserModel::getDataUser());
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->redirectSuccess('login', 'Berhasil logout');
    }

    public function register()
    {
        $level = HakAksesModel::all(); // Ambil level dari basis data
        return view('auth.register', compact('level')); // Kirim level ke view
    }

    public function postRegister(Request $request)
    {
        try {
            $result = UserModel::prosesRegister($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }

            if ($result['success']) {
                return $this->redirectSuccess('login', $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }

            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses registrasi: ' . $e->getMessage()
                ], 500);
            }

            return $this->redirectException($e, 'Terjadi kesalahan saat memproses registrasi');
        }
    }
}
