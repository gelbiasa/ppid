<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function selectData()
    {
        //
    }

    public static function createData()
    {
        //
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function detailData()
    {
        //
    }

    public static function validasiData()
    {
        //
    }
}
