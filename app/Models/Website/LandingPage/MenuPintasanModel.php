<?php

namespace App\Models\Website\LandingPage;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MenuPintasanModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_menu_pintasan_lainya';
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