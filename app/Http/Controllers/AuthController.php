<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\HakAksesModel;
use App\Models\SetUserHakAksesModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use TraitsController;

    public function login()
    {
        if (Auth::check()) {
            // Jika sudah ada hak akses aktif, arahkan sesuai level
            if (session()->has('active_hak_akses_id')) {
                // Ambil user terlebih dahulu
                $user = UserModel::find(Auth::id());
                $levelCode = $user->getRole();
                return redirect('/dashboard' . $levelCode);
            }
            
            // Jika belum ada hak akses aktif, arahkan ke pemilihan level
            return redirect('/pilih-level');
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
                // Cek apakah multi level atau tidak
                if (isset($result['multi_level']) && $result['multi_level']) {
                    return redirect($result['redirect']);
                }
                
                // Jika single level, arahkan sesuai level pengguna
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

    public function pilihLevel()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }
        
        // Ambil user terlebih dahulu
        $user = UserModel::find(Auth::id());
        
        // Ambil hak akses user
        // Gunakan query langsung karena method hakAkses mungkin tidak dikenali
        $hakAkses = DB::table('set_user_hak_akses')
            ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
            ->where('set_user_hak_akses.fk_m_user', $user->user_id)
            ->where('set_user_hak_akses.isDeleted', 0)
            ->where('m_hak_akses.isDeleted', 0)
            ->select('m_hak_akses.*')
            ->get();
        
        // Jika hanya punya 1 level, arahkan langsung
        if ($hakAkses->count() == 1) {
            session(['active_hak_akses_id' => $hakAkses->first()->hak_akses_id]);
            $levelCode = $hakAkses->first()->hak_akses_kode;
            return redirect('/dashboard' . $levelCode);
        }
        
        return view('auth.pilih-level', compact('hakAkses', 'user'));
    }
    
    public function pilihLevelPost(Request $request)
    {
        $request->validate([
            'hak_akses_id' => 'required|exists:m_hak_akses,hak_akses_id'
        ], [
            'hak_akses_id.required' => 'Hak akses harus dipilih',
            'hak_akses_id.exists' => 'Hak akses tidak valid'
        ]);
        
        // Cek apakah user memiliki hak akses tersebut
        $hakAkses = SetUserHakAksesModel::where('fk_m_user', Auth::id())
            ->where('fk_m_hak_akses', $request->hak_akses_id)
            ->where('isDeleted', 0)
            ->first();
            
        if (!$hakAkses) {
            return $this->redirectError('Anda tidak memiliki hak akses tersebut');
        }
        
        // Set hak akses aktif ke session
        session(['active_hak_akses_id' => $request->hak_akses_id]);
        
        // Ambil kode hak akses untuk redirect ke dashboard
        $hakAksesInfo = HakAksesModel::find($request->hak_akses_id);
        $levelCode = $hakAksesInfo->hak_akses_kode;
        
        return redirect('/dashboard' . $levelCode);
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
        $level = HakAksesModel::where('isDeleted', 0)->get(); // Ambil level dari basis data
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