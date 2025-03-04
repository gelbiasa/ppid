<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use TraitsController;

    public function login()
    {
        if (Auth::check()) {
            // Redirect sesuai level pengguna yang login
            $levelCode = Auth::user()->level->level_kode;
            return redirect('/dashboard' . $levelCode);
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = UserModel::prosesLogin($request);
            return response()->json($result);
        }

        return redirect('auth.auth');
    }

    public function getSessionData()
    {
        return response()->json([
            'user_id' => session('user_id'),
            'nama_pengguna' => session('nama_pengguna'),
            'alamat_pengguna' => session('alamat_pengguna'),
            'no_hp_pengguna' => session('no_hp_pengguna'),
            'email_pengguna' => session('email_pengguna'),
            'pekerjaan_pengguna' => session('pekerjaan_pengguna'),
            'nik_pengguna' => session('nik_pengguna'),
            'upload_nik_pengguna' => session('upload_nik_pengguna'),
            'alias' => session('alias'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function register()
    {
        $level = LevelModel::all(); // Fetch level from database
        return view('auth.register', compact('level')); // Pass levels to the view
    }

    public function postRegister(Request $request)
    {
        try {
            $result = UserModel::prosesRegister($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }

            if ($result['success']) {
                return redirect('login')->with('success', $result['message']);
            }

            return back()->withErrors(['error' => $result['message']])->withInput();
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memproses registrasi: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses registrasi: ' . $e->getMessage()])->withInput();
        }
    }
}
