<?php

namespace App\Models\Website\InformasiPublik\KontenDinamis;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpUploadKontenModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_ip_upload_konten';
    protected $primaryKey = 'upload_konten_id';
    protected $fillable = [
        'fk_m_ip_dinamis_konten',
        'uk_judul_konten',
        'uk_tipe_upload_konten',
        'uk_dokumen_konten'
    ];

    public function dinamis_konten()
    {
        return $this->belongsTo(IpDinamisKontenModel::class, 'fk_m_ip_dinamis_konten', 'm_ip_konten_dinamis_id');
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