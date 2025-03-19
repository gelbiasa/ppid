<?php

namespace App\Models\Website\Footer;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KategoriFooterModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_footer';
    protected $primaryKey = 'kategori_footer_id';
    protected $fillable = [
        'kt_footer_kode',
        'kt_footer_nama',
    ];

    // Relasi dengan footer 
    public function footer()
    {
        return $this->hasMany(FooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Fungsi untuk mengambil semua data, sesuai dengan selectData() 
    public static function selectData()
    {
        return self::where('isDeleted', 0)->get();
    }

    // Fungsi untuk membuat data baru, sama seperti createData() 
    public static function createData($request)
    {
        try {
            // Validasi input
            self::validasiData($request);

            DB::beginTransaction();

            // Cek apakah ada data yang sudah dihapus dengan kode/nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where(function ($query) use ($request) {
                    $query->where('kt_footer_kode', $request->kt_footer_kode)
                        ->orWhere('kt_footer_nama', $request->kt_footer_nama);
                })
                ->get();

            // Hapus data yang soft deleted dengan kode/nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'kt_footer_kode',
                'kt_footer_nama'
            ]);

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Footer berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat kategori footer');
        }
    }

    // Fungsi untuk mengupdate data, sama seperti updateData() 
    public static function updateData($request, $id)
    {
        try {
            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Cek apakah ada data yang sudah dihapus dengan kode/nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('kategori_footer_id', '!=', $id)
                ->where(function ($query) use ($request) {
                    $query->where('kt_footer_kode', $request->kt_footer_kode)
                        ->orWhere('kt_footer_nama', $request->kt_footer_nama);
                })
                ->get();

            // Hapus data yang soft deleted dengan kode/nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'kt_footer_kode',
                'kt_footer_nama'
            ]);

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Footer berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui kategori footer');
        }
    }

    // Fungsi untuk menghapus data, sama seperti deleteData() 
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            // Periksa apakah kategori sedang digunakan
            $footerCount = FooterModel::where('fk_m_kategori_footer', $id)
                ->where('isDeleted', 0)
                ->count();

            if ($footerCount > 0) {
                return self::responFormatError(
                    new \Exception('Kategori tidak dapat dihapus karena masih digunakan oleh footer'),
                    'Kategori tidak dapat dihapus karena masih digunakan oleh footer'
                );
            }

            DB::beginTransaction();

            // Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus kategori footer');
        }
    }

    public static function detailData($id)
    {
        try {
            $kategoriFooter = self::findOrFail($id);
            return $kategoriFooter;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data, sama seperti validasiData() 
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'kt_footer_kode' => [
                'required',
                'max:20',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan kode yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_kode', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Kode footer sudah digunakan');
                    }
                }
            ],
            'kt_footer_nama' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan nama yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_nama', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Nama footer sudah digunakan');
                    }
                }
            ],
        ];

        $messages = [
            'kt_footer_kode.required' => 'Kode footer wajib diisi',
            'kt_footer_kode.max' => 'Kode footer maksimal 20 karakter',
            'kt_footer_nama.required' => 'Nama footer wajib diisi',
            'kt_footer_nama.max' => 'Nama footer maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}