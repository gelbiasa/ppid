<?php

namespace App\Models\Website\InformasiPublik\LHKPN;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LhkpnModel extends Model
{
    use TraitsModel;

    protected $table = 'm_lhkpn';
    protected $primaryKey = 'lhkpn_id';
    protected $fillable = [
        'lhkpn_tahun',
        'lhkpn_judul_informasi',
        'lhkpn_deskripsi_informasi'
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
            $query->where(function($q) use ($search) {
                $q->where('lhkpn_tahun', 'like', "%{$search}%")
                  ->orWhere('lhkpn_judul_informasi', 'like', "%{$search}%");
            });
        }

        
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_lhkpn;
            $lhkpn = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $lhkpn->lhkpn_id,
                $lhkpn->lhkpn_judul_informasi
            );

            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat data LHKPN');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $lhkpn = self::findOrFail($id);
            
            $data = $request->m_lhkpn;
            $lhkpn->update($data);

            TransactionModel::createData(
                'UPDATED',
                $lhkpn->lhkpn_id, 
                $lhkpn->lhkpn_judul_informasi 
            );

            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui data LHKPN');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $lhkpn = self::findOrFail($id);
            
            $lhkpn->delete();

            TransactionModel::createData(
                'DELETED',
                $lhkpn->lhkpn_id,
                $lhkpn->lhkpn_judul_informasi
            );
                
            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus data LHKPN');
        }
    }

    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_lhkpn.lhkpn_tahun' => 'required|max:4',
            'm_lhkpn.lhkpn_judul_informasi' => 'required|max:255',
            'm_lhkpn.lhkpn_deskripsi_informasi' => 'required',
        ];

        $messages = [
            'm_lhkpn.lhkpn_tahun.required' => 'Tahun LHKPN wajib diisi',
            'm_lhkpn.lhkpn_tahun.max' => 'Tahun LHKPN maksimal 4 karakter',
            'm_lhkpn.lhkpn_judul_informasi.required' => 'Judul informasi wajib diisi',
            'm_lhkpn.lhkpn_judul_informasi.max' => 'Judul informasi maksimal 255 karakter',
            'm_lhkpn.lhkpn_deskripsi_informasi.required' => 'Deskripsi informasi wajib diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}