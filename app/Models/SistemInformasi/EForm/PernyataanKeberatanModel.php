<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PernyataanKeberatanModel extends Model
{
    use HasFactory, SoftDeletes;

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
        'pk_sudah_dibaca',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function PiDiriSendiri()
    {
        return $this->belongsTo(FormPiDiriSendiriModel::class, 'fk_t_form_pi_diri_sendiri', 'form_pi_diri_sendiri_id');
    }

    public function PiOrangLain()
    {
        return $this->belongsTo(FormPiOrangLainModel::class, 'fk_t_form_pi_orang_lain', 'form_pi_orang_lain_id');
    }

    public function PiOrganisasi()
    {
        return $this->belongsTo(FormPiOrganisasiModel::class, 'fk_t_form_pi_organisasi', 'form_pi_organisasi_id');
    }
}
