<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class NotifVerifikatorModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_verifikator';
    protected $primaryKey = 'notif_verifikator_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_verif',
        'notif_verifikator_form_id',
        'pesan_notif_verif',
        'sudah_dibaca_notif_verif',
        'isDeleted',
        'created_at',
        'deleted_at'
    ];

    public static function createData($formId, $message)
    {
        // Get the current route and determine the form type
        $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
        $kategori = self::menentukanKategoriForm($currentRoute);
        
        NotifVerifikatorModel::create([
            'kategori_notif_verif' => $kategori,
            'notif_verifikator_form_id' => $formId,
            'pesan_notif_verif' => $message,
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