<?php

namespace App\Models\Website\InformasiPublik\Regulasi;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class KategoriRegulasiModel extends Model
{
    use TraitsModel;

    protected $table = 't_kategori_regulasi';
    protected $primaryKey = 'kategori_reg_id';
    protected $fillable = [
        'fk_regulasi_dinamis',
        'kr_kategori_reg_kode',
        'kr_nama_kategori'
    ];

    public function RegulasiDinamis()
    {
        return $this->belongsTo(RegulasiDinamisModel::class, 'fk_regulasi_dinamis', 'regulasi_dinamis_id');
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('RegulasiDinamis')
            ->where('isDeleted', 0);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kr_nama_kategori', 'like', "%{$search}%")
                  ->orWhereHas('RegulasiDinamis', function($subQuery) use ($search) {
                      $subQuery->where('rd_judul_reg_dinamis', 'like', "%{$search}%");
                  });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_kategori_regulasi;
            $kategoriRegulasi = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $kategoriRegulasi->kategori_reg_id,
                $kategoriRegulasi->kr_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($kategoriRegulasi, 'Kategori regulasi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat kategori regulasi');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $kategoriRegulasi = self::findOrFail($id);

            $data = $request->m_kategori_regulasi;
            $kategoriRegulasi->update($data);

            TransactionModel::createData(
                'UPDATED',
                $kategoriRegulasi->kategori_reg_id,
                $kategoriRegulasi->kr_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($kategoriRegulasi, 'Kategori regulasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui kategori regulasi');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $kategoriRegulasi = self::findOrFail($id);

            $kategoriRegulasi->delete();

            TransactionModel::createData(
                'DELETED',
                $kategoriRegulasi->kategori_reg_id,
                $kategoriRegulasi->kr_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($kategoriRegulasi, 'Kategori regulasi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus kategori regulasi');
        }
    }

    public static function detailData($id)
    {
        return self::with('RegulasiDinamis')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_kategori_regulasi.fk_regulasi_dinamis' => 'required|exists:m_regulasi_dinamis,regulasi_dinamis_id',
            'm_kategori_regulasi.kr_kategori_reg_kode' => 'required|max:20',
            'm_kategori_regulasi.kr_nama_kategori' => 'required|max:200',
        ];

        $messages = [
            'm_kategori_regulasi.fk_regulasi_dinamis.required' => 'Regulasi dinamis wajib dipilih',
            'm_kategori_regulasi.fk_regulasi_dinamis.exists' => 'Regulasi dinamis tidak valid',
            'm_kategori_regulasi.kr_kategori_reg_kode.required' => 'Kode kategori regulasi wajib diisi',
            'm_kategori_regulasi.kr_kategori_reg_kode.max' => 'Kode kategori regulasi maksimal 20 karakter',
            'm_kategori_regulasi.kr_nama_kategori.required' => 'Nama kategori regulasi wajib diisi',
            'm_kategori_regulasi.kr_nama_kategori.max' => 'Nama kategori regulasi maksimal 200 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}