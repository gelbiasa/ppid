<?php

namespace App\Models;

use App\Models\Website\WebMenuUrlModel;
use Illuminate\Database\Eloquent\Model;

class WebMenuGlobalModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_global';
    protected $primaryKey = 'web_menu_global_id';

    protected $fillable = [
        'fk_web_menu_url',
        'wmg_nama_default'
    ];

    // Relationships
    public function WebMenuUrl()
    {
        return $this->belongsTo(WebMenuUrlModel::class, 'fk_web_menu_url', 'web_menu_url_id');
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