<?php

namespace App\Models\SistemInformasi\DinamisEForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class JawabanDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 't_jawaban_dinamis';
    protected $primaryKey = 'jawaban_dinamis_id';
    protected $fillable = [
        'fk_t_form_dinamis',
        'fk_t_pertanyaan_dinamis',
        'jd_jawaban',
    ];

    public function FormDinamis()
    {
        return $this->belongsTo(FormDinamisModel::class, 'fk_t_form_dinamis', 'form_dinamis_id');
    }

    public function PertanyaanDinamis()
    {
        return $this->belongsTo(PertanyaanDinamisModel::class, 'fk_t_pertanyaan_dinamis', 'pertanyaan_dinamis_id');
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
