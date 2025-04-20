<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationModel extends Model
{
    use TraitsModel;

    protected $table = 'm_application';
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'app_key',
        'app_nama'
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
