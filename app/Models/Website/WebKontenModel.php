<?php

namespace App\Models\Website;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class WebKontenModel extends Model
{
    use TraitsModel;

    protected $table = 'web_konten';
    protected $primaryKey = 'web_konten_id';

    protected $fillable = [
        'fk_web_menu',
        'wk_judul_konten',
        'wk_deskripsi_konten',
        'wk_status_konten'
    ];

    // Relationships
    public function WebMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
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