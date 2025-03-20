<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KategoriAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_akses';
    protected $primaryKey = 'kategori_akses_id';
    protected $fillable = [
        'mka_judul_kategori'
    ];

    public function aksesCepat()
    {
        return $this->hasMany(AksesCepatModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
    }
    public function pintasanLainnya()
    {
        return $this->hasMany(PintasanLainnyaModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Fungsi untuk mengambil semua data dengan pagination
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Add search functionality
        if (!empty($search)) {
            $query->where('mka_judul_kategori', 'like', "%{$search}%");
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
                'mka_judul_kategori'
            ]);

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->kategori_akses_id,
                $saveData->mka_judul_kategori
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Akses berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Kategori Akses');
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

            // Cek apakah ada data yang sudah dihapus dengan judul yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('kategori_akses_id', '!=', $id)
                ->where('mka_judul_kategori', $request->mka_judul_kategori)
                ->get();

            // Hapus data yang soft deleted dengan judul yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'mka_judul_kategori'
            ]);

            // Update record
            $saveData->update($data);


            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->kategori_akses_id,
                $saveData->mka_judul_kategori
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Akses berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();


            return self::responFormatError($e, 'Gagal memperbarui Kategori Akses');
        }
    }

    // Fungsi untuk menghapus data
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

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
                $saveData->kategori_akses_id,
                $saveData->mka_judul_kategori
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Kategori Akses berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Kategori Akses');
        }
    }

    // Fungsi untuk mendapatkan detail data
    public static function detailData($id)
    {
        try {
            $kategoriAkses = self::findOrFail($id);
            return $kategoriAkses;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'mka_judul_kategori' => [
                'required',
                'max:100',
                // Tambahkan unique rule kecuali untuk record saat ini jika sedang update
                $id ? 'unique:m_kategori_akses,mka_judul_kategori,' . $id . ',kategori_akses_id' : 'unique:m_kategori_akses,mka_judul_kategori'
            ]
        ];

        $messages = [
            'mka_judul_kategori.required' => 'Judul kategori wajib diisi',
            'mka_judul_kategori.max' => 'Judul kategori maksimal 100 karakter',
            'mka_judul_kategori.unique' => 'Judul kategori sudah ada'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }


    // Function API get data AksesCepat
    public static function getDataAksesCepat()
    {
        $arr_data = self::query()
            ->from('m_kategori_akses')
            ->select([
                'kategori_akses_id',
                'mka_judul_kategori'
            ])
            ->where('isDeleted', 0)
            ->where('mka_judul_kategori', 'Akses Menu Cepat')
            ->get()
            ->map(function ($kategori) {
                // Ambil Akses Cepat untuk kategori ini
                $aksesCepat = AksesCepatModel::query()
                    ->select([
                        'akses_cepat_id',
                        'ac_judul',
                        'ac_static_icon',
                        'ac_animation_icon',
                        'ac_url'
                    ])
                    ->where('fk_m_kategori_akses', $kategori->kategori_akses_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function ($akses) {
                        return [
                            'id' => $akses->akses_cepat_id,
                            'judul' => $akses->ac_judul,
                            'static_icon' => $akses->ac_static_icon ? asset('storage/' . AksesCepatModel::STATIC_ICON_PATH . '/' . $akses->ac_static_icon) : null,
                            'animation_icon' => $akses->ac_animation_icon ? asset('storage/' . AksesCepatModel::ANIMATION_ICON_PATH . '/' . $akses->ac_animation_icon) : null,
                            'url' => $akses->ac_url
                        ];
                    });
                return [
                    'kategori_id' => $kategori->kategori_akses_id,
                    'kategori_judul' => $kategori->mka_judul_kategori,
                    'akses_cepat' => $aksesCepat
                ];
            })
            ->toArray();
        return $arr_data;
    }
    // Function API get data PintasanLainnya
    public static function getDataPintasanLainnya()
    {
        $arr_data = self::query()
            ->from('m_kategori_akses')
            ->select([
                'kategori_akses_id',
                'mka_judul_kategori'
            ])
            ->where('isDeleted', 0)
            ->where('mka_judul_kategori', 'Pintasan Lainnya')
            ->get()
            ->map(function ($kategori) {
                // Ambil Pintasan Lainnya untuk kategori ini
                $pintasanLainnya = PintasanLainnyaModel::query()
                    ->select([
                        'pintasan_lainnya_id',
                        'tpl_nama_kategori'
                    ])
                    ->where('fk_m_kategori_akses', $kategori->kategori_akses_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function ($pintasan) {
                        // Ambil detail untuk setiap Pintasan Lainnya
                        $details = DetailPintasanLainnyaModel::query()
                            ->select([
                                'detail_pintasan_lainnya_id',
                                'dpl_judul',
                                'dpl_url'
                            ])
                            ->where('fk_pintasan_lainnya', $pintasan->pintasan_lainnya_id)
                            ->where('isDeleted', 0)
                            ->get()
                            ->map(function ($detail) {
                                return [
                                    'id' => $detail->detail_pintasan_lainnya_id,
                                    'judul' => $detail->dpl_judul,
                                    'url' => $detail->dpl_url
                                ];
                            });

                        return [
                            'pintasan_id' => $pintasan->pintasan_lainnya_id,
                            'nama_kategori' => $pintasan->tpl_nama_kategori,
                            'detail' => $details
                        ];
                    });

                return [
                    'kategori_id' => $kategori->kategori_akses_id,
                    'kategori_judul' => $kategori->mka_judul_kategori,
                    'pintasan' => $pintasanLainnya
                ];
            })
            ->toArray();

        return $arr_data;
    }
}