<?php

namespace App\Models\SistemInformasi\DinamisEForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PertanyaanDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_pertanyaan_dinamis';
    protected $primaryKey = 'pertanyaan_dinamis_id';
    protected $fillable = [
        'fk_t_form_dinamis',
        'pd_nama',
        'pd_jenis',
    ];

    public function FormDinamis()
    {
        return $this->belongsTo(FormDinamsimModel::class, 'fk_t_form_dinamis', 'form_dinamis_id');
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
