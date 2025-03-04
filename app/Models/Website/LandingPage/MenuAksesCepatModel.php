<?php

namespace App\Models\Website\LandingPage;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class MenuAksesCepatModel extends Model
{
    use TraitsModel;

    protected $table = 't_menu_akses_cepat';
    protected $primaryKey = 'menu_akses_id';
    protected $fillable = [
        'mac_judul',
        'mac_icon',
        'mac_url'
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