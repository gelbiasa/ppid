<?php

namespace App\Models\Website\InformasiPublik\PenyelesaianSengketa;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class UploadPSModel extends Model
{
    use TraitsModel;

    protected $table = 't_upload_ps';
    protected $primaryKey = 'upload_ps_id';
    protected $fillable = [
        'fk_m_penyelesaian_sengketa',
        'kategori_upload_ps',
        'upload_ps'
    ];

    public function PenyelesaianSengketa()
    {
        return $this->belongsTo(PenyelesaianSengketaModel::class, 'fk_m_penyelesaian_sengketa', 'penyelesaian_sengketa_id');
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
