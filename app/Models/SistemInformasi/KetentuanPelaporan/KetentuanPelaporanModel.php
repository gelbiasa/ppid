<?php

namespace App\Models\SistemInformasi\KetentuanPelaporan;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class KetentuanPelaporanModel extends Model
{
    use TraitsModel;

    protected $table = 'm_ketentuan_pelaporan';
    protected $primaryKey = 'ketentuan_pelaporan_id';
    protected $fillable = [
        'kp_kategori',
        'kp_deskripsi',
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
