<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class BeritaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_berita_dinamis';
    protected $primaryKey = 'berita_dinamis_id';
    protected $fillable = [
        'bd_nama_submenu',
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
