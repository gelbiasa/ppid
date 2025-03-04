<?php

namespace App\Models\Website\InformasiPublik\TabelDinamis;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class IpSubMenuUtama extends Model
{
    use TraitsModel;

    protected $table = 't_ip_sub_menu_utama';
    protected $primaryKey = 'ip_sub_menu_utama_id';
    protected $fillable = [
        'fk_ip_menu_utama',
        'nama_ip_smu',
        'dokumen_ip_smu'
    ];

    public function IpMenuUtama()
    {
        return $this->belongsTo(IpMenuUtama::class, 'fk_t_ip_menu_utama', 'ip_menu_utama_id');
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
