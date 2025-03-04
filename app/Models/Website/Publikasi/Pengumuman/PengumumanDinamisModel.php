<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

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

    public static function validasiData()
    {
        //
    }
}
