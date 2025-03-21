<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PengumumanDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_pengumuman_dinamis';
    protected $primaryKey = 'pengumuman_dinamis_id';
    protected $fillable = [
        'pd_nama_submenu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        return self::where('isDeleted', 0)->get();
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_pengumuman_dinamis;
            $pengumumanDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $pengumumanDinamis->pengumuman_dinamis_id,
                $pengumumanDinamis->pd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat pengumuman dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $pengumumanDinamis = self::findOrFail($id);
            
            $data = $request->m_pengumuman_dinamis;
            $pengumumanDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $pengumumanDinamis->pengumuman_dinamis_id, 
                $pengumumanDinamis->pd_nama_submenu 
            );

            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui pengumuman dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $pengumumanDinamis = self::findOrFail($id);
            
            $pengumumanDinamis->delete();

            TransactionModel::createData(
                'DELETED',
                $pengumumanDinamis->pengumuman_dinamis_id,
                $pengumumanDinamis->pd_nama_submenu
            );
                
            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus pengumuman dinamis');
        }
    }

    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_pengumuman_dinamis.pd_nama_submenu' => 'required|max:255',
        ];

        $messages = [
            'm_pengumuman_dinamis.pd_nama_submenu.required' => 'Nama submenu pengumuman wajib diisi',
            'm_pengumuman_dinamis.pd_nama_submenu.max' => 'Nama submenu pengumuman maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}