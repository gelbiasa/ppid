<?php

namespace App\Models\Website\InformasiPublik\TabelDinamis;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class IpDinamisTabel extends Model
{
    use TraitsModel;

    protected $table = 'm_ip_dinamis_tabel';
    protected $primaryKey = 'ip_dinamis_tabel_id';
    protected $fillable = [
        'ip_nama_submenu',
        'ip_judul'
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
