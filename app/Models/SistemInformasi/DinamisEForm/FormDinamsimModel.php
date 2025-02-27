<?php

namespace App\Models\SistemInformasi\DinamisEForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormDinamsimModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_dinamis';
    protected $primaryKey = 'form_dinamis_id';
    protected $fillable = [
        'fd_nama',
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
