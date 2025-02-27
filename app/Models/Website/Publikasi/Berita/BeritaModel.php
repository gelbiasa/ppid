<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeritaModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_berita';
    protected $primaryKey = 'berita_id';
    protected $fillable = [
        'fk_m_berita_dinamis',
        'berita_judul',
        'berita_slug',
        'berita_thumbnail',
        'berita_thumbnail_deskripsi',
        'berita_deskripsi',
        'status_berita'
    ];

    public function BeritaDinamis()
    {
        return $this->belongsTo(BeritaDinamisModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
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
