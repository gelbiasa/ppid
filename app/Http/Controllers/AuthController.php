<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) { // jika sudah login, maka redirect ke halaman home 
            return redirect('/dashboardAdmin');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::where('nik_pengguna', $request->username)
                ->orWhere('email_pengguna', $request->username)
                ->orWhere('no_hp_pengguna', $request->username)
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);

                // Simpan data ke sesi
                session([
                    'user_id' => $user->user_id,
                    'nama_pengguna' => $user->nama_pengguna,
                    'alamat_pengguna' => $user->alamat_pengguna,
                    'no_hp_pengguna' => $user->no_hp_pengguna,
                    'email_pengguna' => $user->email_pengguna,
                    'pekerjaan_pengguna' => $user->pekerjaan_pengguna,
                    'nik_pengguna' => $user->nik_pengguna,
                    'upload_nik_pengguna' => $user->upload_nik_pengguna,
                    'alias' => self::generateAlias($user->nama_pengguna), // Alias dari nama pengguna
                ]);

                $redirectUrl = match ($user->level->level_kode) {
                    'ADM' => url('/dashboardAdmin'),
                    'RPN' => url('/dashboardResponden'),
                    'MPU' => url('/dashboardMPU'),
                    'VFR' => url('/dashboardVFR'),
                    default => url('/login')
                };

                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => $redirectUrl,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login Gagal, Periksa Kredensial Anda',
            ]);
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

    private static function generateAlias($nama)
    {
        $words = explode(' ', $nama); // Pisahkan nama berdasarkan spasi
        $alias = '';

        foreach ($words as $word) {
            if (strlen($alias . ' ' . $word) > 15) {
                // Jika menambahkan kata akan melebihi 15 karakter, singkat dengan inisial
                $alias .= ' ' . strtoupper(substr($word, 0, 1)) . '.';
                break; // Hentikan iterasi setelah menambahkan inisial
            } else {
                $alias .= ($alias == '' ? '' : ' ') . $word;
            }
        }

        return trim($alias);
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
            $validator = Validator::make($request->all(), [
                'username' => 'required|min:4|max:20|unique:m_user,username',
                'nama' => 'required|min:2|max:50',
                'password' => 'required|min:5|max:20|confirmed',
                'password_confirmation' => 'required',
                'level_id' => 'required|exists:m_level,level_id',
                'no_hp' => 'required|digits_between:4,15',
                'email' => 'required|email|unique:m_user,email'
            ], [
                'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
                'username.min' => 'Username minimal harus 4 karakter.',
                'username.max' => 'Username maksimal 20 karakter.',
                'nama.min' => 'Nama minimal harus 2 karakter.',
                'nama.max' => 'Nama maksimal 50 karakter.',
                'password.min' => 'Password minimal harus 5 karakter.',
                'password.max' => 'Password maksimal 20 karakter.',
                'password.confirmed' => 'Verifikasi password tidak sesuai dengan password baru.',
                'no_hp.required' => 'Nomor handphone wajib diisi.',
                'no_hp.digits_between' => 'Nomor handphone harus terdiri dari 4 hingga 15 digit.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            UserModel::create([
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => bcrypt($request->password),
                'level_id' => $request->level_id,
                'no_hp' => $request->no_hp,
                'email' => $request->email
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Register Berhasil',
                'redirect' => url('login')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses registrasi'
            ], 500);
        }
    }
}
