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
        'log_transaction_aktivitas_id',
        'log_transaction_aktivitas',
        'log_transaction_level',
        'log_transaction_pelaku',
        'log_transaction_tanggal_aktivitas',
    ];

    // Di TransactionModel
    public static function createData($tipeTransaksi, $aktivitasId, $detailAktivitas)
    {
        // Dapatkan controller dan action saat ini
        $route = Route::current();
        $controller = $route->getController();

        // Dapatkan tipe transaksi
        $transactionType = strtoupper($tipeTransaksi);

        // Tentukan jenis form/menu berdasarkan controller
        $formType = self::menentukanTipeForm($controller);

        // Generate pesan aktivitas
        $aktivitas = self::generateAktivitas(
            Auth::user()->nama_pengguna,
            $transactionType,
            $formType,
            $detailAktivitas
        );

        // Buat record log transaksi
        self::create([
            'log_transaction_jenis' => $transactionType,
            'log_transaction_aktivitas_id' => $aktivitasId,
            'log_transaction_aktivitas' => $aktivitas,
            'log_transaction_level' => Auth::user()->level->hak_akses_nama,
            'log_transaction_pelaku' => session('alias'),
            'log_transaction_tanggal_aktivitas' => now()
        ]);
    }

    private static function menentukanTipeForm($controller)
    {
        if (!$controller) {
            return 'tidak ada controller';
        }

        // Cek apakah controller memiliki properti breadcrumb
        if (property_exists($controller, 'breadcrumb')) {
            return $controller->breadcrumb;
        }

        // Jika tidak ada breadcrumb, coba ambil dari pagename
        if (property_exists($controller, 'pagename')) {
            $segments = explode('/', $controller->pagename);
            $lastSegment = end($segments);
            // Hapus kata 'Controller' dari nama
            return str_replace('Controller', '', $lastSegment);
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

    private static function generateAktivitas($namaPengguna, $tipeTransaksi, $tipeForm, $detailAktivitas)
    {
        $aksi = self::mendapatkanAksi($tipeTransaksi);

        // Jika ada detail aktivitas, tambahkan ke pesan
        $detailTambahan = $detailAktivitas ? " $detailAktivitas" : '';

        return "{$namaPengguna} {$aksi} {$tipeForm}{$detailTambahan}";
    }
}
