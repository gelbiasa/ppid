<?php

namespace App\Models\Website\LayananInformasi;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class LIDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_li_dinamis';
    protected $primaryKey = 'li_dinamis_id';
    protected $fillable = [
        'li_dinamsi_kode',
        'li_dinamis_nama',
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
