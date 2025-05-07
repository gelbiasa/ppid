<?php

namespace App\Http\Middleware;

use App\Models\HakAkses\SetHakAksesModel;
use App\Models\Website\WebMenuModel;
use App\Models\Website\WebMenuUrlModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckUserPermission
{
    public function handle(Request $request, Closure $next, $permission = 'view')
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();
        $userId = $user->user_id;
        
        // Ambil active hak akses dari session
        $activeHakAksesId = session('active_hak_akses_id');
        
        // Jika tidak ada active hak akses, cari yang pertama
        if (!$activeHakAksesId) {
            $hakAkses = DB::table('set_user_hak_akses')
                ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
                ->where('set_user_hak_akses.fk_m_user', $userId)
                ->where('set_user_hak_akses.isDeleted', 0)
                ->where('m_hak_akses.isDeleted', 0)
                ->first();
                
            if ($hakAkses) {
                session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
                $activeHakAksesId = $hakAkses->hak_akses_id;
            } else {
                return redirect('pilih-level')->with('error', 'Anda belum memiliki level akses');
            }
        }
        
        // Ambil informasi hak akses
        $hakAkses = DB::table('m_hak_akses')
            ->where('hak_akses_id', $activeHakAksesId)
            ->where('isDeleted', 0)
            ->first();
            
        if (!$hakAkses) {
            return redirect('pilih-level')->with('error', 'Anda belum memilih level akses');
        }
        
        $hakAksesKode = $hakAkses->hak_akses_kode;
        $hakAksesId = $hakAkses->hak_akses_id;

        // Super Admin selalu punya akses penuh
        if ($hakAksesKode === 'SAR') {
            return $next($request);
        }

        $currentPath = $request->path();

        // Cari menu URL berdasarkan nama URL
        $menuUrl = WebMenuUrlModel::where('wmu_nama', $currentPath)
            ->orWhere('wmu_nama', str_replace('adminweb/', '', $currentPath))
            ->first();

        // Jika URL tidak ditemukan, izinkan akses
        if (!$menuUrl) {
            return $next($request);
        }

        // Cari menu yang terkait dengan URL dan level
        $menu = WebMenuModel::whereHas('WebMenuGlobal', function($query) use ($menuUrl) {
            $query->where('fk_web_menu_url', $menuUrl->web_menu_url_id);
        })
        ->where('fk_m_hak_akses', $hakAksesId)
        ->where('wm_status_menu', 'aktif')
        ->where('isDeleted', 0)
        ->first();

        // Jika menu tidak ditemukan, izinkan akses
        if (!$menu) {
            return $next($request);
        }

        // Cek hak akses
        $userHakAkses = SetHakAksesModel::where('ha_pengakses', $userId)
            ->where('fk_web_menu', $menu->web_menu_id)
            ->first();

        // Buat prefix untuk kolom hak akses
        $hakField = 'ha_' . $permission;

        if (!$userHakAkses || $userHakAkses->$hakField != 1) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini'
                ], 403);
            }

            return response()->view('errors.403', [
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini',
                'activeMenu' => request()->segment(1),
            ], 403);
        }

        return $next($request);
    }
}