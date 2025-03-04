<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class PernyataanKeberatanModel extends Model
{
    use TraitsModel;

    protected $table = 't_pernyataan_keberatan';
    protected $primaryKey = 'pernyataan_keberatan_id';
    protected $fillable = [
        'fk_t_form_pk_orang_lain',
        'pk_kategori_pemohon',
        'pk_kategori_aduan',
        'pk_bukti_aduan',
        'pk_nama_pengguna',
        'pk_alamat_pengguna',
        'pk_pekerjaan_pengguna',
        'pk_no_hp_pengguna',
        'pk_email_pengguna',
        'pk_upload_nik_pengguna',
        'pk_alasan_pengajuan_keberatan',
        'pk_kasus_posisi',
        'pk_status',
        'pk_jawaban',
        'pk_alasan_penolakan',
        'pk_sudah_dibaca'
    ];

    public function PkOrangLain()
    {
        return $this->belongsTo(FormPiOrangLainModel::class, 'fk_t_form_pk_orang_lain', 'form_pk_orang_lain_id');
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
