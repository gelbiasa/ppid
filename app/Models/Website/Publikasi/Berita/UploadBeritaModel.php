<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class UploadBeritaModel extends Model
{
    use TraitsModel;

    protected $table = 't_upload_berita';
    protected $primaryKey = 'upload_berita_id';
    protected $fillable = [
        'fk_t_berita',
        'ub_type',
        'ub_value'
    ];

    public function Berita()
    {
        return $this->belongsTo(BeritaModel::class, 'fk_t_berita', 'berita_id');
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
