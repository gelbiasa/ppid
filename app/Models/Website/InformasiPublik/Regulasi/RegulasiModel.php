<?php

namespace App\Models\Website\InformasiPublik\Regulasi;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegulasiModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_regulasi';
    protected $primaryKey = 'regulasi_id';
    protected $fillable = [
        'fk_m_kategori_regulasi',
        'reg_judul',
        'reg_sinopsis',
        'reg_dokumen',
        'reg_tipe_dokumen'
    ];
    public function KategoriRegulasi()
    {
        return $this->belongsTo(KategoriRegulasiModel::class, 'fk_m_kategori_regulasi',  'kategori_reg_id');
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