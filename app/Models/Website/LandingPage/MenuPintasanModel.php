<?php

namespace App\Models\Website\LandingPage;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class MenuPintasanModel extends Model
{
    use TraitsModel;

    protected $table = 't_menu_pintasan_lainnya';
    protected $primaryKey = 'menu_pintasan_id';
    protected $fillable = [
        'mpl_judul',
        'mpl_url'
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