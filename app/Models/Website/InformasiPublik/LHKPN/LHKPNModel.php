<?php

namespace App\Models\Website\InformasiPublik\LHKPN;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class LHKPNModel extends Model
{
    use TraitsModel;

    protected $table = 't_lhkpn';
    protected $primaryKey = 'lhkpn_id';
    protected $fillable = [
        'lhkpn_tahun',
        'lhkpn_judul_informasi',
        'lhkpn_deskripsi_informasi',
        'lhkpn_nama_karyawan',
        'lhkpn_file',
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
