<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TransactionModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_transaction';
    protected $primaryKey = 'log_transaction_id';
    public $timestamps = false;
    protected $fillable = [
        'log_transaction_jenis',
        'log_transaction_aktivitas',
        'log_transaction_level',
        'log_transaction_pelaku',
        'log_transaction_tanggal_aktivitas',
    ];

    public static function createData($model = null)
    {
        $transactionType = self::menentukanTipeTransaction($model);
        $activity = self::generateAktivitas($transactionType);
        
        TransactionModel::create([
            'log_transaction_jenis' => $transactionType,
            'log_transaction_aktivitas' => $activity,
            'log_transaction_level' => Auth::user()->level->level_nama,
            'log_transaction_pelaku' => session('alias'),
            'log_transaction_tanggal_aktivitas' => now()
        ]);
    }
    
    private static function menentukanTipeTransaction($model)
    {
        // Default to CREATED if no model is provided
        if (!$model) {
            return 'CREATED';
        }
        
        // Check model state to determine transaction type
        if ($model->deleted_by !== null) {
            return 'DELETED';
        } elseif ($model->updated_by !== null && $model->created_by !== $model->updated_by) {
            return 'UPDATED';
        } else {
            return 'CREATED';
        }
    }
    
    private static function generateAktivitas($transactionType)
    {
        $userName = Auth::user()->nama_pengguna;
        $formType = self::menentukanTipeForm();
        $action = self::mendapatkanAksi($transactionType);
        
        return "{$userName} {$action} {$formType}";
    }
    
    private static function menentukanTipeForm()
    {
        $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
        
        // E-Form handling
        if (strpos($currentRoute, 'PermohonanInformasi') !== false) {
            return 'E-Form Permohonan Informasi';
        } elseif (strpos($currentRoute, 'PernyataanKeberatan') !== false) {
            return 'E-Form Pernyataan Keberatan';
        } elseif (strpos($currentRoute, 'PengaduanMasyarakat') !== false) {
            return 'E-Form Pengaduan Masyarakat';
        }
        
        // Menu management handling
        elseif (strpos($currentRoute, 'menu-utama') !== false && !strpos($currentRoute, 'management')) {
            return 'Menu Utama';
        } elseif (strpos($currentRoute, 'submenu') !== false) {
            return 'Submenu';
        } elseif (strpos($currentRoute, 'menu-management') !== false) {
            return 'Menu Management';
        }
        
        return 'data';
    }
    
    private static function mendapatkanAksi($transactionType)
    {
        switch ($transactionType) {
            case 'CREATED':
                return 'membuat/mengajukan';
            case 'UPDATED':
                return 'memperbaharui';
            case 'DELETED':
                return 'menghapus';
            default:
                return 'melakukan aksi pada';
        }
    }
}