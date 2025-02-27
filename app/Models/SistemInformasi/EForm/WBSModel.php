<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WBSModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_wbs';
    protected $primaryKey = 'wbs_id';
    protected $fillable = [
        'wbs_kategori_aduan',
        'wbs_bukti_aduan',
        'wbs_nama_tanpa_gelar',
        'wbs_nik_pengguna',
        'wbs_upload_nik_pengguna',
        'wbs_email_pengguna',
        'wbs_no_hp_pengguna',
        'wbs_jenis_laporan',
        'wbs_yang_dilaporkan',
        'wbs_jabatan',
        'wbs_waktu_kejadian',
        'wbs_lokasi_kejadian',
        'wbs_kronologis_kejadian',
        'wbs_bukti_pendukung',
        'wbs_catatan_tambahan',
        'wbs_status',
        'wbs_jawaban',
        'wbs_alasan_penolakan',
        'wbs_sudah_dibaca'
        
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
