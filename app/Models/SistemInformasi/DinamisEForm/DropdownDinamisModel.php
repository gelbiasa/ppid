<?php

namespace App\Models\SistemInformasi\DinamisEForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class DropdownDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 't_dropdown_dinamis';
    protected $primaryKey = 'dropdown_dinamis_id';
    protected $fillable = [
        'fk_t_pertanyaan_dinamis',
        'dd_value',
    ];

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
