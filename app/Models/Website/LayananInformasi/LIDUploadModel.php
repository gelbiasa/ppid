<?php

namespace App\Models\Website\LayananInformasi;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LIDUploadModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_lid_upload';
    protected $primaryKey = 'lid_upload_id';
    protected $fillable = [
        'fk_m_li_dinamis',
        'lid_upload_type',
        'lid_upload_value'
    ];

    public function LiDinamis()
    {
        return $this->belongsTo(LIDinamisModel::class, 'fk_m_li_dinamis', 'li_dinamis_id');
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
