<?php

namespace App\Models\Website\Footer;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_kategori_footer';
    protected $primaryKey = 'kategori_footer_id';
    protected $fillable = [
        'kt_footer_kode',
        'kt_footer_nama',
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
