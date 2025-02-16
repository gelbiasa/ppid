<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPiOangLainModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_pi_orang_lain';
    protected $primaryKey = 'form_pi_orang_lain_id';
    protected $fillable = [
        'pi_nama_pengguna_informasi',
        'pi_alamat_pengguna_informasi',
        'pi_no_hp_pengguna_informasi',
        'pi_email_pengguna_informasi',
        'pi_upload_nik_pengguna_informasi',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
}
