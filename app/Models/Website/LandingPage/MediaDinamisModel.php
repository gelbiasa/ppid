<?php

namespace App\Models\Website\LandingPage;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaDinamisModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_media_dinamis';
    protected $primaryKey = 'media_dinamis_id';
    protected $fillable = [
        'md_judul',
        'md_tipe_media',
        'md_upload_media'
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