<?php

namespace App\Models\Website\InformasiPublik\Regulasi;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class KategoriRegulasiModel extends Model
{
    use TraitsModel;

    protected $table = ' t_kategori_regulasi';
    protected $primaryKey = 'kategori_reg_id';
    protected $fillable = [
        'fk_regulasi_dinamis',
        'kr_kategori_reg_kode',
        'kr_nama_kategori'
    ];
     public function RegulasiDinamis()
    {
        return $this->belongsTo(RegulasiDinamisModel::class, 'fk_regulasi_dinamis', 'regulasi_dinamis_id');
    }

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