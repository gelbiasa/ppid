<?php

namespace App\Models\Website\InformasiPublik\Regulasi;

use App\Models\BaseModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegulasiDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_regulasi_dinamis';
    protected $primaryKey = 'regulasi_dinamis_id';
    protected $fillable = [
        'fk_web_menu',
        'rd_judul_reg_dinamis'
    ];
    public function Menu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu',  'web_menu_id');
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