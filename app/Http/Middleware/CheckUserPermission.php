<?php

namespace App\Http\Middleware;

use App\Models\HakAkses\HakAksesModel;
use App\Models\Website\WebMenuModel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission  Jenis izin (view, create, update, delete)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = 'view')
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();
        
        // Super Admin selalu punya akses penuh
        if ($user->level->level_kode === 'SAR') {
            return $next($request);
        }
        
        // Dapatkan URL saat ini
        $currentPath = $request->path();
        
        // Coba cari menu berdasarkan URL secara langsung
        $menu = WebMenuModel::where('wm_menu_url', $currentPath)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->first();
            
        // Jika tidak ditemukan, coba cek apakah URL ini berisi URL menu lain sebagai prefix
        if (!$menu) {
            // Ambil semua menu aktif
            $allMenus = WebMenuModel::where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->get();
                
            // Cek apakah URL saat ini merupakan bagian dari URL menu yang ada
            foreach ($allMenus as $menuItem) {
                if (!empty($menuItem->wm_menu_url) && strpos($currentPath, $menuItem->wm_menu_url) === 0) {
                    $menu = $menuItem;
                    break;
                }
            }
        }
        
        // Jika masih tidak ditemukan, izinkan akses (untuk route non-menu)
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
            
            // Siapkan variabel yang diperlukan untuk template layout
            $breadcrumb = (object) [
                'title' => 'Akses Ditolak',
                'list' => ['Home', 'Error', '403 - Akses Ditolak']
            ];

            $activeMenu = 'HakAkses';
            
            $page = (object) [
                'title' => 'Akses Ditolak'
            ];
            
            // Tambahkan semua variabel yang diperlukan untuk template
            return response()->view('errors.403', [
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini',
                'activeMenu' => $activeMenu,
                'breadcrumb' => $breadcrumb,
                'page' => $page
            ], 403);
        }
        
        return $next($request);
    }
}
