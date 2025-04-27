<?php

namespace App\Models\Website\InformasiPublik\TabelDinamis;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class IpMenuUtama extends Model
{
    use TraitsModel;

    protected $table = 't_ip_menu_utama';
    protected $primaryKey = 'ip_menu_utama_id';
    protected $fillable = [
        'fk_t_ip_dinamis_tabel',
        'nama_ip_mu',
        'dokumen_ip_mu'
    ];

    public function IpDinamisTabel()
    {
        return $this->belongsTo(IpDinamisTabel::class, 'fk_t_ip_dinamis_tabel', 'ip_dinamis_tabel_id');
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
