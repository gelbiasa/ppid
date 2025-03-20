<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AksesCepatModel extends Model
{
    use TraitsModel;

    protected $table = 't_akses_cepat';
    protected $primaryKey = 'akses_cepat_id';

    // Konstanta untuk path folder icon
    const STATIC_ICON_PATH = 'akses_cepat_static_icons';
    const ANIMATION_ICON_PATH = 'akses_cepat_animation_icons';

    // Kolom yang dapat diisi
    protected $fillable = [
        'fk_m_kategori_akses',
        'ac_judul',
        'ac_static_icon', 
        'ac_animation_icon',
        'ac_url'
    ];

    // Relasi dengan Kategori Akses
    public function kategoriAkses()
    {
        return $this->belongsTo(KategoriAksesModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    
    /**
     * Metode untuk mengambil data dengan optional filtering
     */
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::with('kategoriAkses')
            ->where('isDeleted', 0);
    
        // Add search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ac_judul', 'like', "%{$search}%");
            });
        }
    
        return $query->paginate($perPage);
    }

    /**
     * Metode untuk membuat data baru
     */
    public static function createData($request)
    {
        try {
            // Validasi input
            self::validasiData($request);

            DB::beginTransaction();

            // Persiapan data
            $data = $request->only([
                'fk_m_kategori_akses',
                'ac_judul',
                'ac_url'
            ]);

            // Proses upload static icon
            if ($request->hasFile('ac_static_icon')) {
                $staticIconPath = $request->file('ac_static_icon')->store(self::STATIC_ICON_PATH, 'public');
                $data['ac_static_icon'] = basename($staticIconPath);
            }

            // Proses upload animation icon
            if ($request->hasFile('ac_animation_icon')) {
                $animationIconPath = $request->file('ac_animation_icon')->store(self::ANIMATION_ICON_PATH, 'public');
                $data['ac_animation_icon'] = basename($animationIconPath);
            }

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->akses_cepat_id,
                $saveData->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Akses Cepat berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Akses Cepat');
        }
    }

    /**
     * Metode untuk mengupdate data
     */
    public static function updateData($request, $id)
    {
        try {
            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Persiapan data
            $data = $request->only([
                'fk_m_kategori_akses',
                'ac_judul',
                'ac_url'
            ]);

            // Proses upload static icon
            if ($request->hasFile('ac_static_icon')) {
                // Hapus static icon lama jika ada
                if ($saveData->ac_static_icon) {
                    self::deleteIconFile($saveData->ac_static_icon, self::STATIC_ICON_PATH);
                }

                // Upload static icon baru
                $staticIconPath = $request->file('ac_static_icon')->store(self::STATIC_ICON_PATH, 'public');
                $data['ac_static_icon'] = basename($staticIconPath);
            }

            // Proses upload animation icon
            if ($request->hasFile('ac_animation_icon')) {
                // Hapus animation icon lama jika ada
                if ($saveData->ac_animation_icon) {
                    self::deleteIconFile($saveData->ac_animation_icon, self::ANIMATION_ICON_PATH);
                }

                // Upload animation icon baru
                $animationIconPath = $request->file('ac_animation_icon')->store(self::ANIMATION_ICON_PATH, 'public');
                $data['ac_animation_icon'] = basename($animationIconPath);
            }

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->akses_cepat_id,
                $saveData->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Akses Cepat berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Akses Cepat');
        }
    }

    /**
     * Metode untuk menghapus data (soft delete)
     */
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Hapus file icon jika ada
            if ($saveData->ac_static_icon) {
                self::deleteIconFile($saveData->ac_static_icon, self::STATIC_ICON_PATH);
            }

            if ($saveData->ac_animation_icon) {
                self::deleteIconFile($saveData->ac_animation_icon, self::ANIMATION_ICON_PATH);
            }

            // Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->akses_cepat_id,
                $saveData->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Akses Cepat berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Akses Cepat');
        }
    }

    /**
     * Metode untuk mendapatkan detail data
     */
    public static function detailData($id)
    {
        try {
            $aksesCepat = self::with('kategoriAkses')->findOrFail($id);
            return $aksesCepat;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Metode untuk validasi data
     */
    public static function validasiData($request, $id = null)
{
    // Aturan validasi dasar
    $rules = [
        'fk_m_kategori_akses' => 'required|exists:m_kategori_akses,kategori_akses_id',
        'ac_judul' => 'required|max:100',
        'ac_url' => 'required|url|max:100',
    ];

    // Jika create baru atau update dengan file baru
    if ($id === null) {
        // Untuk create baru
        $rules['ac_static_icon'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3048';
        $rules['ac_animation_icon'] = 'nullable|mimes:gif|max:3048';
    } else {
        // Untuk update
        if ($request->hasFile('ac_static_icon')) {
            $rules['ac_static_icon'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:3048';
        }
        
        if ($request->hasFile('ac_animation_icon')) {
            $rules['ac_animation_icon'] = 'mimes:gif|max:3048';
        }
    }

    $messages = [
        'fk_m_kategori_akses.required' => 'Kategori akses wajib dipilih',
        'fk_m_kategori_akses.exists' => 'Kategori akses tidak valid',
        'ac_judul.required' => 'Judul akses cepat wajib diisi',
        'ac_judul.max' => 'Judul akses cepat maksimal 100 karakter',
        'ac_url.required' => 'URL akses cepat wajib diisi',
        'ac_url.url' => 'URL akses cepat harus berupa URL yang valid',
        'ac_url.max' => 'URL akses cepat maksimal 100 karakter',
        'ac_static_icon.required' => 'Ikon statis wajib diunggah',
        'ac_static_icon.image' => 'Ikon statis harus berupa gambar',
        'ac_static_icon.mimes' => 'Ikon statis hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
        'ac_static_icon.max' => 'Ukuran ikon statis maksimal 3MB',
        'ac_animation_icon.mimes' => 'Ikon animasi hanya boleh berupa file gif',
        'ac_animation_icon.max' => 'Ukuran ikon animasi maksimal 3MB',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    return true;
}

    /**
     * Helper method untuk menghapus file ikon
     */
    private static function deleteIconFile($filename, $path)
    {
        try {
            $fullPath = $path . '/' . $filename;
            if (Storage::disk('public')->exists($fullPath)) {
                Storage::disk('public')->delete($fullPath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}