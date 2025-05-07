<?php

namespace App\Models;

use App\Models\Log\TransactionModel;
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

    // Relasi ke tabel set_user_hak_akses
    public function hakAksesSet()
    {
        return $this->hasMany(SetUserHakAksesModel::class, 'fk_m_user', 'user_id');
    }

    // Mendapatkan semua hak akses user
    public function hakAkses()
    {
        return $this->belongsToMany(HakAksesModel::class, 'set_user_hak_akses', 'fk_m_user', 'fk_m_hak_akses')
            ->where('set_user_hak_akses.isDeleted', 0)
            ->where('m_hak_akses.isDeleted', 0);
    }

    // Mendapatkan hak akses aktif saat ini
    public function level()
    {
        $activeHakAksesId = session('active_hak_akses_id');

        return $this->belongsToMany(HakAksesModel::class, 'set_user_hak_akses', 'fk_m_user', 'fk_m_hak_akses')
            ->wherePivot('isDeleted', 0)
            ->where('m_hak_akses.isDeleted', 0)
            ->when($activeHakAksesId, function ($query) use ($activeHakAksesId) {
                return $query->where('m_hak_akses.hak_akses_id', $activeHakAksesId);
            });
    }

    // Accessor untuk mengambil level sebagai single model, bukan collection
    public function getLevelAttribute()
    {
        return $this->level()->first();
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public function getRoleName(): string
    {
        $level = $this->getActiveHakAkses();
        return $level ? $level->hak_akses_nama : '';
    }

    public function hasRole($role): bool
    {
        $level = $this->getActiveHakAkses();
        return $level && $level->hak_akses_kode == $role;
    }

    /* Mendapatkan Kode Role */
    public function getRole()
    {
        $level = $this->getActiveHakAkses();
        return $level ? $level->hak_akses_kode : '';
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

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengguna', 'like', "%{$search}%")
                    ->orWhere('email_pengguna', 'like', "%{$search}%")
                    ->orWhere('nik_pengguna', 'like', "%{$search}%")
                    ->orWhere('no_hp_pengguna', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validasi data
            self::validasiData($request);

            $data = $request->m_user;
            $hakAksesId = $request->hak_akses_id;

            // Proses upload file KTP jika ada
            if ($request->hasFile('upload_nik_pengguna')) {
                $fileName = self::uploadFile(
                    $request->file('upload_nik_pengguna'),
                    'upload_nik'
                );
                $data['upload_nik_pengguna'] = $fileName;
            }

            // Hash password
            $data['password'] = Hash::make($request->password);

            // Simpan data user ke database
            $user = self::create($data);

            // Buat relasi di tabel set_user_hak_akses
            SetUserHakAksesModel::create([
                'fk_m_user' => $user->user_id,
                'fk_m_hak_akses' => $hakAksesId
            ]);

            // Log transaksi
            TransactionModel::createData(
                'CREATED',
                $user->user_id,
                $user->nama_pengguna
            );

            DB::commit();

            return self::responFormatSukses($user, 'Pengguna berhasil dibuat');
        } catch (ValidationException $e) {
            DB::rollBack();
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($fileName)) {
                self::removeFile($fileName);
            }
            return self::responFormatError($e, 'Gagal membuat pengguna');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            // Validasi data
            self::validasiDataUpdate($request, $id);

            $user = self::findOrFail($id);
            $data = $request->m_user;

            // Proses upload file KTP jika ada
            if ($request->hasFile('upload_nik_pengguna')) {
                // Hapus file lama jika ada
                if (!empty($user->upload_nik_pengguna)) {
                    self::removeFile($user->upload_nik_pengguna);
                }

                $fileName = self::uploadFile(
                    $request->file('upload_nik_pengguna'),
                    'upload_nik'
                );
                $data['upload_nik_pengguna'] = $fileName;
            }

            // Update password jika ada
            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            // Update data user
            $user->update($data);

            // Log transaksi
            TransactionModel::createData(
                'UPDATED',
                $user->user_id,
                $user->nama_pengguna
            );

            DB::commit();

            return self::responFormatSukses($user, 'Pengguna berhasil diperbarui');
        } catch (ValidationException $e) {
            DB::rollBack();
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($fileName)) {
                self::removeFile($fileName);
            }
            return self::responFormatError($e, 'Gagal memperbarui pengguna');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $user = self::findOrFail($id);

            // Hapus user (soft delete)
            $user->delete();

            // Log transaksi
            TransactionModel::createData(
                'DELETED',
                $user->user_id,
                $user->nama_pengguna
            );

            DB::commit();

            return self::responFormatSukses($user, 'Pengguna berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus pengguna');
        }
    }

    public static function detailData($id)
    {
        return self::with(['hakAkses'])->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required',
            'm_user.nama_pengguna' => 'required|min:2|max:50',
            'm_user.email_pengguna' => 'required|email|unique:m_user,email_pengguna',
            'm_user.no_hp_pengguna' => 'required|digits_between:4,15',
            'm_user.alamat_pengguna' => 'required|string',
            'm_user.pekerjaan_pengguna' => 'required|string',
            'm_user.nik_pengguna' => 'required|digits:16|unique:m_user,nik_pengguna',
            'hak_akses_id' => 'required|exists:m_hak_akses,hak_akses_id',
        ];

        // Jika file KTP ada, validasi file
        if ($request->hasFile('upload_nik_pengguna')) {
            $rules['upload_nik_pengguna'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        $messages = [
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
            'hak_akses_id.required' => 'Level pengguna wajib dipilih.',
            'hak_akses_id.exists' => 'Level pengguna tidak valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function validasiDataUpdate($request, $id)
    {
        $rules = [
            'm_user.nama_pengguna' => 'required|min:2|max:50',
            'm_user.email_pengguna' => 'required|email|unique:m_user,email_pengguna,' . $id . ',user_id',
            'm_user.no_hp_pengguna' => 'required|digits_between:4,15',
            'm_user.alamat_pengguna' => 'required|string',
            'm_user.pekerjaan_pengguna' => 'required|string',
            'm_user.nik_pengguna' => 'required|digits:16|unique:m_user,nik_pengguna,' . $id . ',user_id',
        ];

        // Jika password diisi, validasi password
        if (!empty($request->password)) {
            $rules['password'] = 'required|min:5|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        // Jika file KTP ada, validasi file
        if ($request->hasFile('upload_nik_pengguna')) {
            $rules['upload_nik_pengguna'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        $messages = [
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
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function prosesLogin($request)
    {
        $user = self::where('nik_pengguna', $request->username)
            ->orWhere('email_pengguna', $request->username)
            ->orWhere('no_hp_pengguna', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);

            // Ambil semua hak akses yang dimiliki user
            $hakAkses = $user->hakAkses()->get();

            // Jika user memiliki lebih dari 1 hak akses
            if ($hakAkses->count() > 1) {
                // Simpan data user ke session
                $userData = self::getDataUser($user);
                session($userData);

                // Kembalikan data hak akses untuk ditampilkan di form pilih level
                return [
                    'success' => true,
                    'message' => 'Login Berhasil',
                    'multi_level' => true,
                    'hak_akses' => $hakAkses,
                    'redirect' => url('/pilih-level'),
                    'user' => $user
                ];
            }
            // Jika user hanya memiliki 1 hak akses
            elseif ($hakAkses->count() == 1) {
                // Set hak akses aktif
                session(['active_hak_akses_id' => $hakAkses->first()->hak_akses_id]);

                // Simpan data user ke session
                $userData = self::getDataUser($user);
                session($userData);

                // Perbaikan routing - sesuaikan dengan definisi route yang ada
                $levelCode = $hakAkses->first()->hak_akses_kode;
                $redirectUrl = url('/dashboard' . $levelCode);

                return [
                    'success' => true,
                    'message' => 'Login Berhasil',
                    'multi_level' => false,
                    'redirect' => $redirectUrl,
                    'user' => $user
                ];
            }
            // Jika user tidak memiliki hak akses sama sekali
            else {
                Auth::logout();
                return [
                    'success' => false,
                    'message' => 'Akun Anda tidak memiliki hak akses yang aktif',
                ];
            }
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

            // Ambil hak akses yang dipilih
            $hakAksesId = $request->hak_akses_id;

            // Simpan data user ke database
            $user = self::create($data);

            // Buat relasi di tabel set_user_hak_akses
            SetUserHakAksesModel::create([
                'fk_m_user' => $user->user_id,
                'fk_m_hak_akses' => $hakAksesId
            ]);

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
            'hak_akses_id' => 'required|exists:m_hak_akses,hak_akses_id',
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
            'hak_akses_id.required' => 'Level pengguna wajib dipilih.',
            'hak_akses_id.exists' => 'Level pengguna tidak valid.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getUsersByLevel($levelId, $perPage = null, $search = '')
    {
        $query = self::query()
            ->join('set_user_hak_akses', 'm_user.user_id', '=', 'set_user_hak_akses.fk_m_user')
            ->where('set_user_hak_akses.fk_m_hak_akses', $levelId)
            ->where('m_user.isDeleted', 0)
            ->where('set_user_hak_akses.isDeleted', 0)
            ->select('m_user.*');

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pengguna', 'like', "%{$search}%")
                    ->orWhere('email_pengguna', 'like', "%{$search}%")
                    ->orWhere('nik_pengguna', 'like', "%{$search}%")
                    ->orWhere('no_hp_pengguna', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    // Helper method untuk mengambil hak akses aktif sekarang
    public function getActiveHakAkses()
    {
        $activeHakAksesId = session('active_hak_akses_id');

        if (!$activeHakAksesId) {
            // Jika tidak ada hak akses aktif di session, ambil yang pertama
            $hakAkses = $this->hakAkses()->first();
            if ($hakAkses) {
                session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
                return $hakAkses;
            }
            return null;
        }

        return $this->hakAkses()->where('m_hak_akses.hak_akses_id', $activeHakAksesId)->first();
    }
}
