<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPiPeroranganModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_pi_perorangan';
    protected $primaryKey = 'form_pi_perorangan_id';
    protected $fillable = [
        'fk_t_form_pi_orang_lain',
        'pi_kategori_perorangan',
        'pi_nama_pengguna',
        'pi_alamat_pengguna',
        'pi_no_hp_pengguna',
        'pi_email_pengguna',
        'pi_upload_nik_pengguna',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function PiOrangLain()
    {
        return $this->belongsTo(FormPiOangLainModel::class, 'fk_t_form_pi_orang_lain', 'form_pi_orang_lain_id');
    }
}
