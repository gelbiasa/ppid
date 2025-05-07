<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;

class SetUserHakAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'set_user_hak_akses';
    protected $primaryKey = 'set_user_hak_akses_id';
    protected $fillable = [
        'fk_m_hak_akses',
        'fk_m_user'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public function HakAkses() {
        return $this->belongsTo(HakAksesModel::class, 'fk_m_hak_akses', 'hak_akses_id');
    }

    public function User() {
        return $this->belongsTo(UserModel::class, 'fk_m_user', 'user_id');
    }

    public static function getUsersByHakAkses($hakAksesId)
    {
        return self::where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->with(['User' => function($query) {
                $query->where('isDeleted', 0);
            }])
            ->get();
    }

    public static function createData($userId, $hakAksesId)
    {
        try {
            DB::beginTransaction();

            $userHakAkses = self::create([
                'fk_m_user' => $userId,
                'fk_m_hak_akses' => $hakAksesId
            ]);

            // Log transaksi
            TransactionModel::createData(
                'CREATED',
                $userHakAkses->set_user_hak_akses_id,
                'Menambahkan hak akses ke pengguna'
            );

            DB::commit();

            return self::responFormatSukses($userHakAkses, 'Hak akses berhasil ditambahkan ke pengguna');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menambahkan hak akses');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $userHakAkses = self::findOrFail($id);

            $userHakAkses->delete();

            // Log transaksi
            TransactionModel::createData(
                'DELETED',
                $userHakAkses->set_user_hak_akses_id,
                'Menghapus hak akses dari pengguna'
            );

            DB::commit();

            return self::responFormatSukses($userHakAkses, 'Hak akses berhasil dihapus dari pengguna');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus hak akses');
        }
    }
}