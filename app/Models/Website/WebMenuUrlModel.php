<?php

namespace App\Models\Website;

use App\Models\ApplicationModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class WebMenuUrlModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_url';
    protected $primaryKey = 'web_menu_url_id';

    protected $fillable = [
        'fk_m_application',
        'wmu_nama',
        'wmu_keterangan'
    ];

    // Relationships
    public function Application()
    {
        return $this->belongsTo(ApplicationModel::class, 'fk_m_application', 'application_id');
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

    // Di WebMenuUrlModel.php
    public function scopePpidOnly($query)
    {
        return $query->whereHas('Application', function ($q) {
            $q->where('app_key', 'app ppid');
        });
    }
}
