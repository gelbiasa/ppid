<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class FormPkOrangLainModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pk_orang_lain';
    protected $primaryKey = 'form_pk_orang_lain_id';
    protected $fillable = [
        'pk_nama_kuasa_pemohon',
        'pi_alamat_kuasa_pemohon',
        'pi_no_hp_kuasa_pemohon',
        'pk_email_kuasa_pemohon',
        'pk_identitas_kuasa_pemohon'
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
