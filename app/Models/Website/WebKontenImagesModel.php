<?php

namespace App\Models\Website;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class WebKontenImagesModel extends Model
{
    use TraitsModel;

    protected $table = 'web_konten_images';
    protected $primaryKey = 'konten_images_id';

    protected $fillable = [
        'fk_web_konten',
        'wki_image_webkonten'
    ];

    public function WebKonten()
    {
        return $this->belongsTo(WebKontenModel::class, 'fk_web_konten', 'web_konten_id');
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