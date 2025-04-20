<?php

namespace App\Http\Middleware;

use App\Models\HakAkses\HakAksesModel;
use App\Models\Website\WebMenuModel;
use App\Models\Website\WebMenuUrlModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckUserPermission
{
    public function handle(Request $request, Closure $next, $permission = 'view')
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();
        $levelKode = $user->level->level_kode;
        $levelId = $user->fk_m_level;

        // Super Admin selalu punya akses penuh
        if ($levelKode === 'SAR') {
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
        ->where('fk_m_level', $levelId)
        ->where('wm_status_menu', 'aktif')
        ->where('isDeleted', 0)
        ->first();

        // Jika menu tidak ditemukan, izinkan akses
        if (!$menu) {
            return $next($request);
        }

        // Cek hak akses
        $hakAkses = HakAksesModel::where('ha_pengakses', $user->user_id)
            ->where('fk_web_menu', $menu->web_menu_id)
            ->first();

        // Buat prefix untuk kolom hak akses
        $hakField = 'ha_' . $permission;

        if (!$hakAkses || $hakAkses->$hakField != 1) {
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