<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserModel extends Authenticatable implements JWTSubject
{
    use TraitsModel;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'fk_m_hak_akses',
        'password',
        'nama_pengguna',
        'alamat_pengguna',
        'no_hp_pengguna',
        'email_pengguna',
        'pekerjaan_pengguna',
        'nik_pengguna',
        'upload_nik_pengguna'
    ];

    protected $hidden = ['password']; // Tidak ditampilkan saat select
    protected $casts = ['password' => 'hashed']; // Password akan di-hash secara otomatis

    // Relasi ke tabel level
    public function level()
    {
        return $this->belongsTo(HakAksesModel::class, 'fk_m_hak_akses', 'hak_akses_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public function getRoleName(): string
    {
        return $this->level->hak_akses_nama;
    }

    public function hasRole($role): bool
    {
        return $this->level->hak_akses_kode == $role;
    }

    /* Mendapatkan Kode Role */
    public function getRole()
    {
        return $this->level->hak_akses_kode;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function prosesLogin($request)
    {
        $user = self::where('nik_pengguna', $request->username)
            ->orWhere('email_pengguna', $request->username)
            ->orWhere('no_hp_pengguna', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);

            // Simpan data ke sesi menggunakan method getDataUser
            $userData = self::getDataUser($user);
            session($userData);

            // Perbaikan routing - sesuaikan dengan definisi route yang ada
            $levelCode = $user->level->hak_akses_kode;
            $redirectUrl = url('/dashboard' . $levelCode);

            return [
                'success' => true,
                'message' => 'Login Berhasil',
                'redirect' => $redirectUrl,
                'user' => $user  // Tambahkan user ke dalam array hasil
            ];
        }

        return [
            'success' => false,
            'message' => 'Login Gagal, Periksa Kredensial Anda',
        ];
    }

    public static function getDataUser($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        return [
            'user_id' => $user->user_id,
            'nama_pengguna' => $user->nama_pengguna,
            'alamat_pengguna' => $user->alamat_pengguna,
            'no_hp_pengguna' => $user->no_hp_pengguna,
            'email_pengguna' => $user->email_pengguna,
            'pekerjaan_pengguna' => $user->pekerjaan_pengguna,
            'nik_pengguna' => $user->nik_pengguna,
            'upload_nik_pengguna' => $user->upload_nik_pengguna,
            'alias' => self::generateAlias($user->nama_pengguna), // Alias dari nama pengguna
        ];
    }

    public static function generateAlias($nama)
    {
        $words = explode(' ', $nama); // Pisahkan nama berdasarkan spasi
        $alias = '';

        foreach ($words as $word) {
            if (strlen($alias . ' ' . $word) > 15) {
                // Jika menambahkan kata akan melebihi 15 karakter, singkat dengan inisial
                $alias .= ' ' . strtoupper(substr($word, 0, 1)) . '.';
                break;
            } else {
                $alias .= ($alias == '' ? '' : ' ') . $word;
            }
        }

        return trim($alias);
    }

    public static function prosesRegister($request)
    {
        try {
            self::validasiRegistrasi($request);

            DB::beginTransaction();

            $data = $request->m_user;

            $fileName = null;
            if ($request->hasFile('upload_nik_pengguna')) {
                $fileName = self::uploadFile(
                    $request->file('upload_nik_pengguna'),
                    'upload_nik'
                );
            }

            // Tambahkan data tambahan yang tidak ada di request
            $data['upload_nik_pengguna'] = $fileName;
            $data['password'] = Hash::make($request->password);

            // Simpan data ke database
            $user = self::create($data);

            DB::commit();

            return self::responFormatSukses($user, 'Register Berhasil', [
                'redirect' => url('login')
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollback();
            if (isset($fileName)) {
                self::removeFile($fileName);
            }
            return self::responFormatError($e, 'Terjadi kesalahan saat memproses registrasi');
        }
    }

    public static function validasiRegistrasi($request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
            'upload_nik_pengguna' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'm_user.nama_pengguna' => 'required|min:2|max:50',
            'm_user.email_pengguna' => 'required|email|unique:m_user,email_pengguna',
            'm_user.no_hp_pengguna' => 'required|digits_between:4,15',
            'm_user.alamat_pengguna' => 'required|string',
            'm_user.pekerjaan_pengguna' => 'required|string',
            'm_user.nik_pengguna' => 'required|digits:16|unique:m_user,nik_pengguna',
            'm_user.fk_m_hak_akses' => 'required|exists:m_hak_akses,hak_akses_id',
        ], [
            'password.min' => 'Password minimal harus 5 karakter.',
            'password.confirmed' => 'Verifikasi password tidak sesuai dengan password baru.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'upload_nik_pengguna.required' => 'Upload foto KTP wajib dilakukan.',
            'upload_nik_pengguna.image' => 'File harus berupa gambar.',
            'upload_nik_pengguna.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'upload_nik_pengguna.max' => 'Ukuran gambar maksimal 2MB.',
            'm_user.nama_pengguna.min' => 'Nama minimal harus 2 karakter.',
            'm_user.nama_pengguna.max' => 'Nama maksimal 50 karakter.',
            'm_user.email_pengguna.required' => 'Email wajib diisi.',
            'm_user.email_pengguna.email' => 'Format email tidak valid.',
            'm_user.email_pengguna.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
            'm_user.no_hp_pengguna.required' => 'Nomor handphone wajib diisi.',
            'm_user.no_hp_pengguna.digits_between' => 'Nomor handphone harus terdiri dari 4 hingga 15 digit.',
            'm_user.alamat_pengguna.required' => 'Alamat wajib diisi.',
            'm_user.pekerjaan_pengguna.required' => 'Pekerjaan wajib diisi.',
            'm_user.nik_pengguna.required' => 'NIK wajib diisi.',
            'm_user.nik_pengguna.digits' => 'NIK harus terdiri dari 16 digit.',
            'm_user.nik_pengguna.unique' => 'NIK sudah terdaftar.',
            'm_user.fk_m_hak_akses.required' => 'Level pengguna wajib dipilih.',
            'm_user.fk_m_hak_akses.exists' => 'Level pengguna tidak valid.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
