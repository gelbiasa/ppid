<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPiOrganisasiModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_pi_organisasi';
    protected $primaryKey = 'form_pi_organisasi_id';
    protected $fillable = [
        'pi_nama_organisasi',
        'pi_no_telp_organisasi',
        'pi_email_atau_medsos_organisasi',
        'pi_nama_narahubung',
        'pi_no_telp_narahubung',
        'pi_identitas_narahubung',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
}
