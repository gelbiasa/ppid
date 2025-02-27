<?php

namespace App\Models\Website\LandingPage;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalerigambarModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = ' t_galeri_gambar';
    protected $primaryKey = 'galeri_gambar_id';
    protected $fillable = [
        'fk_m_geleri_dinamis',
        'gg_gambar_galeri'
    ];
    public function GaleriDinamis()
    {
        return $this->belongsTo(GaleriDinamisModel::class, 'fk_m_geleri_dinamis', 'galeri_dinamis_id');
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