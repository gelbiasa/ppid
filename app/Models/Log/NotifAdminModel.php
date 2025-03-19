<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;

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
        'deleted_by',
        'deleted_at'
    ];

    // Relasi dengan Permohonan Informasi
    public function t_permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasiModel::class, 'notif_admin_form_id', 'permohonan_informasi_id');
    }

    public static function createData($formId, $message)
    {
        $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
        $kategori = self::menentukanKategoriForm($currentRoute);

        return self::create([
            'kategori_notif_admin' => $kategori,
            'notif_admin_form_id' => $formId,
            'pesan_notif_admin' => $message,
            'created_at' => now(),
            'isDeleted' => 0
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
            return 'Notification';
        }
    }

    public static function tandaiDibaca($id)
    {
        $notifikasi = self::findOrFail($id);
        $notifikasi->sudah_dibaca_notif_admin = now();
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusNotifikasi($id)
    {
        $notifikasi = self::findOrFail($id);

        // Cek apakah notifikasi sudah dibaca
        if (!$notifikasi->sudah_dibaca_notif_admin) {
            return [
                'success' => false,
                'message' => 'Notifikasi harus ditandai dibaca terlebih dahulu sebelum dihapus'
            ];
        }

        $notifikasi->isDeleted = 1;
        $notifikasi->deleted_at = now();
        $notifikasi->deleted_by = session('alias') ?? 'System';
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ];
    }
    public static function tandaiSemuaDibaca()
    {
        $notifikasi = self::where('kategori_notif_admin', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNull('sudah_dibaca_notif_admin')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada notifikasi yang dapat ditandai.'
            ];
        }

        foreach ($notifikasi as $item) {
            $item->sudah_dibaca_notif_admin = now();
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusSemuaDibaca()
    {
        $notifikasi = self::where('kategori_notif_admin', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNotNull('sudah_dibaca_notif_admin')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada Notifikasi yang telah dibaca. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.'
            ];
        }

        foreach ($notifikasi as $item) {
            $item->isDeleted = 1;
            $item->deleted_at = now();
            $item->deleted_by = session('alias') ?? 'System';
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi yang telah dibaca berhasil dihapus'
        ];
    }
}
