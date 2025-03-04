<?php

namespace App\Models\SistemInformasi\DinamisEForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class PertanyaanDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 't_pertanyaan_dinamis';
    protected $primaryKey = 'pertanyaan_dinamis_id';
    protected $fillable = [
        'fk_t_form_dinamis',
        'pd_nama',
        'pd_jenis',
    ];

    public function FormDinamis()
    {
        return $this->belongsTo(FormDinamisModel::class, 'fk_t_form_dinamis', 'form_dinamis_id');
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
