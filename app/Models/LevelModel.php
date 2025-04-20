<?php

namespace App\Models;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LevelModel extends Model
{
    use TraitsModel;

    protected $table = 'm_level';
    protected $primaryKey = 'level_id';
    protected $fillable = [
        'level_kode',
        'level_nama'
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
                $q->where('level_kode', 'like', "%{$search}%")
                  ->orWhere('level_nama', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_level;
            $level = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $level->level_id,
                $level->level_nama
            );

            DB::commit();

            return self::responFormatSukses($level, 'Level berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat level');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $level = self::findOrFail($id);

            $data = $request->m_level;
            $level->update($data);

            TransactionModel::createData(
                'UPDATED',
                $level->level_id,
                $level->level_nama
            );

            DB::commit();

            return self::responFormatSukses($level, 'Level berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui level');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $level = self::findOrFail($id);

            $level->delete();

            TransactionModel::createData(
                'DELETED',
                $level->level_id,
                $level->level_nama
            );

            DB::commit();

            return self::responFormatSukses($level, 'Level berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus level');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_level.level_kode' => 'required|max:50',
            'm_level.level_nama' => 'required|max:255',
        ];

        $messages = [
            'm_level.level_kode.required' => 'Kode level wajib diisi',
            'm_level.level_kode.max' => 'Kode level maksimal 50 karakter',
            'm_level.level_nama.required' => 'Nama level wajib diisi',
            'm_level.level_nama.max' => 'Nama level maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}