<?php

namespace App\Models\SistemInformasi\KategoriForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class KategoriFormModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_form';
    protected $primaryKey = 'kategori_form_id';
    protected $fillable = [
        'kf_nama',
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
