<?php

namespace App\Models\Website;

use App\Models\Website\WebKontenModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebMenuModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_menu';
    protected $primaryKey = 'web_menu_id';


    protected $fillable = [
        'wm_parent_id',
        'wm_urutan_menu',
        'wm_menu_nama',
        'wm_menu_url',
        'wm_status_menu',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];


    public function submenus()
    {
        return $this->hasMany(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }


    public function parentMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function konten()
    {
        return $this->hasOne(WebKontenModel::class, 'fk_web_menu', 'web_menu_id');
    }
}