<?php
namespace App\Models\Website;
use App\Models\Website\WebMenuModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebKontenModel extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'web_konten';
    protected $primaryKey = 'web_konten_id';
    
  
    
    protected $fillable = [
        'fk_web_menu',
        'wk_judul_konten',
        'wk_deskripsi_konten',
        'wk_status_konten',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function menu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
    }
}