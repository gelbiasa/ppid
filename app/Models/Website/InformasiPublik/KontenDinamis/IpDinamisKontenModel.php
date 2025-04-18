<?php

namespace App\Models\Website\InformasiPublik\KontenDinamis;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class IpDinamisKontenModel extends  Model
{
    use TraitsModel;

    protected $table = 'm_ip_dinamis_konten';
    protected $primaryKey = 'ip_dinamis_konten_id';
    protected $fillable = [
        'kd_nama_konten_dinamis'
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