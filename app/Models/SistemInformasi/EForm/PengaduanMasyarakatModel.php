<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class PengaduanMasyarakatModel extends Model
{
    use TraitsModel;

    protected $table = 't_pengaduan_masyarakat';
    protected $primaryKey = 'pengaduan_masyarakat_id';
    protected $fillable = [
        'pm_kategori_aduan',
        'pm_bukti_aduan',
        'pm_nama_tanpa_gelar',
        'pm_nik_pengguna',
        'pm_upload_nik_pengguna',
        'pm_email_pengguna',
        'pm_no_hp_pengguna',
        'pm_jenis_laporan',
        'pm_yang_dilaporkan',
        'pm_jabatan',
        'pm_waktu_kejadian',
        'pm_lokasi_kejadian',
        'pm_kronologis_kejadian',
        'pm_bukti_pendukung',
        'pm_catatan_tambahan',
        'pm_status',
        'pm_jawaban',
        'pm_alasan_penolakan',
        'pm_sudah_dibaca'
        
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
