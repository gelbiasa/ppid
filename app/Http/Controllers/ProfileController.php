<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Home', 'Profile']
        ];

        $page = (object) [
            'title' => 'Data Profil Pengguna'
        ];

        $activeMenu = 'profile'; // Set the active menu

        return view('profile.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update_pengguna(Request $request, string $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_pengguna' => 'required|string|max:100',
            'alamat_pengguna' => 'required|string|max:100',
            'no_hp_pengguna' => 'required|string|unique:m_user,no_hp_pengguna,' . $id . ',user_id',
            'email_pengguna' => 'required|email|unique:m_user,email_pengguna,' . $id . ',user_id',
            'pekerjaan_pengguna' => 'required|string',
            'nik_pengguna' => 'required|string|unique:m_user,nik_pengguna,' . $id . ',user_id',
            'upload_nik_pengguna' => 'nullable|image|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mengambil pengguna berdasarkan ID
        $user = UserModel::find($id);

        // Update data pengguna
        $user->nama_pengguna = $request->nama_pengguna;
        $user->email_pengguna = $request->email_pengguna;
        $user->no_hp_pengguna = $request->no_hp_pengguna;
        $user->pekerjaan_pengguna = $request->pekerjaan_pengguna;
        $user->nik_pengguna = $request->nik_pengguna;
        $user->alamat_pengguna = $request->alamat_pengguna;

        // Handle file upload
        if ($request->hasFile('upload_nik_pengguna')) {
            // Delete old file if exists
            if ($user->upload_nik_pengguna) {
                Storage::delete('public/' . $user->upload_nik_pengguna);
            }

            // Generate unique filename
            $file = $request->file('upload_nik_pengguna');
            $filename = 'upload_nik/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $file->storeAs('public', $filename);
            
            // Save filename to database
            $user->upload_nik_pengguna = $filename;
        }

        // Simpan perubahan
        $user->save();

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function update_password(Request $request, string $id)
    {
        // Custom validation rules
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:5', // Password minimal 5 karakter
            'new_password_confirmation' => 'required|same:new_password', // Verifikasi password harus sama dengan password baru
        ], [
            'new_password.min' => 'Password minimal harus 5 karakter', // Pesan kesalahan kustom
            'new_password_confirmation.same' => 'Verifikasi password yang anda masukkan tidak sesuai dengan password baru', // Pesan kesalahan kustom
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            // Cek error untuk new_password dan new_password_confirmation
            if ($validator->errors()->has('new_password')) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error_type', 'new_password'); // Tetap di tab "Ubah Password"
            }

            if ($validator->errors()->has('new_password_confirmation')) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error_type', 'new_password_confirmation'); // Tetap di tab "Ubah Password"
            }
        }

        // Ambil user berdasarkan ID
        $user = UserModel::find($id);

        // Cek apakah password lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai'])
                ->with('error_type', 'current_password'); // Tetap di tab "Ubah Password"
        }

        // Jika validasi lolos, ubah password user
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }
}
