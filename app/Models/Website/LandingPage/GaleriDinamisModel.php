<?php

namespace App\Models\Website\LandingPage;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GaleriDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_galeri_dinamis';
    protected $primaryKey = 'galeri_dinamis_id';
    protected $fillable = [
        'gd_judul_album'
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