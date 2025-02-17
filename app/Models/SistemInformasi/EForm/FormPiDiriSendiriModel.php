<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPiDiriSendiriModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_pi_diri_sendiri';
    protected $primaryKey = 'form_pi_diri_sendiri_id';
    protected $fillable = [
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
}
