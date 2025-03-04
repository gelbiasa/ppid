<?php

namespace App\Models\Website\InformasiPublik\KontenDinamis;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class IpUploadKontenModel extends Model
{
    use TraitsModel;

    protected $table = 't_ip_upload_konten';
    protected $primaryKey = 'ip_upload_konten_id';
    protected $fillable = [
        'fk_m_ip_dinamis_konten',
        'uk_judul_konten',
        'uk_tipe_upload_konten',
        'uk_dokumen_konten'
    ];

    public function IpDinamisKonten()
    {
        return $this->belongsTo(IpDinamisKontenModel::class, 'fk_m_ip_dinamis_konten', 'ip_dinamis_konten_id');
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