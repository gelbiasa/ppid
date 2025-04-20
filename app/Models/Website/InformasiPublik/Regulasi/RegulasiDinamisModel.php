<?php

namespace App\Models\Website\InformasiPublik\Regulasi;
use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegulasiDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_regulasi_dinamis';
    protected $primaryKey = 'regulasi_dinamis_id';
    protected $fillable = [
        'rd_judul_reg_dinamis'
    ];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where('rd_judul_reg_dinamis', 'like', "%{$search}%");
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_regulasi_dinamis;
            $regulasiDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $regulasiDinamis->regulasi_dinamis_id,
                $regulasiDinamis->rd_judul_reg_dinamis
            );

            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat regulasi dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $regulasiDinamis = self::findOrFail($id);
            
            $data = $request->m_regulasi_dinamis;
            $regulasiDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $regulasiDinamis->regulasi_dinamis_id, 
                $regulasiDinamis->rd_judul_reg_dinamis 
            );

            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui regulasi dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $regulasiDinamis = self::findOrFail($id);
            
            $regulasiDinamis->update([
                'isDeleted' => 1,
                'deleted_at' => now(),
                'deleted_by' => auth()->user()->id ?? null
            ]);

            TransactionModel::createData(
                'DELETED',
                $regulasiDinamis->regulasi_dinamis_id,
                $regulasiDinamis->rd_judul_reg_dinamis
            );
                
            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus regulasi dinamis');
        }
    }
    
    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_regulasi_dinamis.rd_judul_reg_dinamis' => 'required|max:150',
        ];

        $messages = [
            'm_regulasi_dinamis.rd_judul_reg_dinamis.required' => 'Judul regulasi dinamis wajib diisi',
            'm_regulasi_dinamis.rd_judul_reg_dinamis.max' => 'Judul regulasi dinamis maksimal 150 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}