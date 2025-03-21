<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BeritaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_berita_dinamis';
    protected $primaryKey = 'berita_dinamis_id';
    protected $fillable = [
        'bd_nama_submenu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Relasi dengan Berita
    public function berita()
    {
        return $this->hasMany(BeritaModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
    }

    public static function selectData($perPage = 10, $search = '')
{
    $query = self::query()
        ->where('isDeleted', 0);

    // Add search functionality
    if (!empty($search)) {
        $query->where('bd_nama_submenu', 'like', "%{$search}%");
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

            // Cek apakah ada data yang sudah dihapus dengan nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('bd_nama_submenu', $request->bd_nama_submenu)
                ->get();

            // Hapus data yang soft deleted dengan nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'bd_nama_submenu'
            ]);

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Berita Dinamis');
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

            // Cek apakah ada data yang sudah dihapus dengan nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('berita_dinamis_id', '!=', $id)
                ->where('bd_nama_submenu', $request->bd_nama_submenu)
                ->get();

            // Hapus data yang soft deleted dengan nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'bd_nama_submenu'
            ]);

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Berita Dinamis');
        }
    }

    // Fungsi untuk menghapus data
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            // Periksa apakah submenu sedang digunakan
            $beritaCount = BeritaModel::where('fk_m_berita_dinamis', $id)
                ->where('isDeleted', 0)
                ->count();

            if ($beritaCount > 0) {
                return self::responFormatError(
                    new \Exception('Submenu tidak dapat dihapus karena masih digunakan oleh berita'),
                    'Submenu tidak dapat dihapus karena masih digunakan oleh berita'
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
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Berita Dinamis');
        }
    }

    // Fungsi untuk detail data
    public static function detailData($id)
    {
        try {
            $beritaDinamis = self::findOrFail($id);
            return $beritaDinamis;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'bd_nama_submenu' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan nama yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('bd_nama_submenu', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('berita_dinamis_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Nama submenu berita sudah digunakan');
                    }
                }
            ],
        ];

        $messages = [
            'bd_nama_submenu.required' => 'Nama submenu berita wajib diisi',
            'bd_nama_submenu.max' => 'Nama submenu berita maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}