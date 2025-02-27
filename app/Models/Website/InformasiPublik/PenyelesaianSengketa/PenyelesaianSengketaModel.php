<?php

namespace App\Models\Website\InformasiPublik\PenyelesaianSengketa;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenyelesaianSengketaModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_penyelesaian_sengketa';
    protected $primaryKey = 'penyelesaian_sengketa_id';
    protected $fillable = [
        'ps_kode',
        'ps_nama',
        'ps_deskripsi',
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
