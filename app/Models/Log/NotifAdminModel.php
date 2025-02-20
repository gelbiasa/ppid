<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class NotifAdminModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_admin';
    protected $primaryKey = 'notif_admin_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_admin',
        'notif_admin_form_id',
        'pesan_notif_admin',
        'sudah_dibaca_notif_admin',
        'isDeleted',
        'created_at',
        'deleted_at'
    ];

    public static function createData($formId, $message)
    {
        // Get the current route and determine the form type
        $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
        $kategori = self::menentukanKategoriForm($currentRoute);
        
        NotifAdminModel::create([
            'kategori_notif_admin' => $kategori,
            'notif_admin_form_id' => $formId,
            'pesan_notif_admin' => $message,
            'created_at' => now()
        ]);
    }
    
    private static function menentukanKategoriForm($route)
    {
        if (strpos($route, 'PermohonanInformasi') !== false) {
            return 'E-Form Permohonan Informasi';
        } elseif (strpos($route, 'PernyataanKeberatan') !== false) {
            return 'E-Form Pernyataan Keberatan';
        } elseif (strpos($route, 'PengaduanMasyarakat') !== false) {
            return 'E-Form Pengaduan Masyarakat';
        } else {
            // Default fallback
            return 'Notification';
        }
    }
}