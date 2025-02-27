<?php

namespace App\Models\Website\LayananInformasi;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LIDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

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
