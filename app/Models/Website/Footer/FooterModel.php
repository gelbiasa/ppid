<?php

namespace App\Models\Website\Footer;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FooterModel extends Model
{
    use TraitsModel;

    protected $table = 't_footer';
    protected $primaryKey = 'footer_id';
    protected $fillable = [
        'fk_m_kategori_footer',
        'f_judul_footer',
        'f_icon_footer',
        'f_url_footer',
    ];

    // Konstanta untuk path folder icon
    const ICON_PATH = 'footer_icons';

    // Relasi dengan kategori footer
    public function kategoriFooter()
    {
        return $this->belongsTo(KategoriFooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Fungsi untuk mengambil semua data dengan pagination
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::with('kategoriFooter')
            ->where('isDeleted', 0);
    
        // Add search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('f_judul_footer', 'like', "%{$search}%")
                  ->orWhereHas('kategoriFooter', function($subQuery) use ($search) {
                      $subQuery->where('kt_footer_nama', 'like', "%{$search}%");
                  });
            });
        }
    
        return $query->paginate($perPage);
    }
    // Fungsi untuk membuat data baru
    public static function createData($request)
    {
        try {
            // Validasi input
            self::validasiData($request);

            DB::beginTransaction();

            // Persiapan data
            $data = $request->only([
                'fk_m_kategori_footer',
                'f_judul_footer',
                'f_url_footer'
            ]);

            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                $iconPath = $request->file('f_icon_footer')->store(self::ICON_PATH, 'public');
                $data['f_icon_footer'] = basename($iconPath); // Simpan hanya nama file
            }

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Footer berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat footer');
        }
    }

    // Fungsi untuk mengupdate data
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
                'fk_m_kategori_footer',
                'f_judul_footer',
                'f_url_footer'
            ]);

            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                // Hapus ikon lama jika ada
                if ($saveData->f_icon_footer) {
                    self::deleteIconFile($saveData->f_icon_footer);
                }

                // Upload ikon baru
                $iconPath = $request->file('f_icon_footer')->store(self::ICON_PATH, 'public');
                $data['f_icon_footer'] = basename($iconPath); // Simpan hanya nama file
            }

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Footer berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui footer');
        }
    }

    // Fungsi untuk menghapus data
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Hapus file ikon jika ada
            if ($saveData->f_icon_footer) {
                self::deleteIconFile($saveData->f_icon_footer);
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
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus footer');
        }
    }

    // Fungsi untuk mendapatkan detail data
    public static function detailData($id)
    {
        try {
            $footer = self::with('kategoriFooter')->findOrFail($id);
            return $footer;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'fk_m_kategori_footer' => 'required|exists:m_kategori_footer,kategori_footer_id',
            'f_judul_footer' => 'required|max:100',
            'f_url_footer' => 'nullable|url|max:100',
            'f_icon_footer' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],
        ];

        $messages = [
            'fk_m_kategori_footer.required' => 'Kategori footer wajib dipilih',
            'fk_m_kategori_footer.exists' => 'Kategori footer tidak valid',
            'f_judul_footer.required' => 'Judul footer wajib diisi',
            'f_judul_footer.max' => 'Judul footer maksimal 100 karakter',
            'f_url_footer.url' => 'URL footer harus berupa URL yang valid',
            'f_url_footer.max' => 'URL footer maksimal 100 karakter',
            'f_icon_footer.image' => 'Ikon harus berupa gambar',
            'f_icon_footer.mimes' => 'Ikon hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            'f_icon_footer.max' => 'Ukuran ikon maksimal 2MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    // Helper method untuk menghapus file ikon
    private static function deleteIconFile($filename)
    {
        try {
            $path = self::ICON_PATH . '/' . $filename;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}