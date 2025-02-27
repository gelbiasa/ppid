<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengaduanMasyarakatModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_pengaduan_masyarakat';
    protected $primaryKey = 'pengaduan_masyarakat_id';
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
