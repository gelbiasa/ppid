<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class PengumumanModel extends Model
{
    use TraitsModel;

    protected $table = 't_pengumuman';
    protected $primaryKey = 'pengumuman_id';
    protected $fillable = [
        'fk_m_pengumuman_dinamis',
        'peg_judul',
        'peg_slug',
        'peg_deskripsi',
        'status_pengumuman',
    ];

    public function PengumumanDinamis()
    {
        return $this->belongsTo(PengumumanDinamisModel::class, 'fk_m_pengumuman_dinamis', 'pengumuman_dinamis_id');
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
