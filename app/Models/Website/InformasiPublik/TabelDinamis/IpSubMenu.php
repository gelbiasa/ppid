<?php

namespace App\Models\Website\InformasiPublik\TabelDinamis;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpSubMenu extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_ip_sub_menu';
    protected $primaryKey = 'ip_sub_menu_id';
    protected $fillable = [
        'fk_t_ip_sub_menu_utama',
        'nama_ip_sm',
        'dokumen_ip_sm'
    ];

    public function IpSubMenuUtana()
    {
        return $this->belongsTo(IpSubMenuUtama::class, 'fk_t_ip_sub_menu_utama', 'ip_sub_menu_id');
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
