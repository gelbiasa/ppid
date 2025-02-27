<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanPerawatanModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_permohonan_perawatan';
    protected $primaryKey = 'permohonan_perawatan_id';
    protected $fillable = [
        'pp_kategori_aduan',
        'pp_bukti_aduan',
        'pp_nama_pengguna',
        'pp_no_hp_pengguna',
        'pp_email_pengguna',
        'pp_unit_kerja',
        'pp_perawatan_yang_diusulkan',
        'pp_keluhan_kerusakan',
        'pp_lokasi_perawatan',
        'pp_foto_kondisi',
        'pp_status',
        'pp_jawaban',
        'pp_alasan_penolakan',
        'pp_sudah_dibaca'
        
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
