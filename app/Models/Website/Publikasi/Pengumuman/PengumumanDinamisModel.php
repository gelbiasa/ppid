<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengumumanDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_pengumuman_dinamis';
    protected $primaryKey = 'pengumuman_dinamis_id';
    protected $fillable = [
        'pd_nama_submenu',
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
