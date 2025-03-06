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
        
        // Dapatkan URL saat ini dan hilangkan prefix adminweb/menu-management
        $currentPath = str_replace('adminweb/menu-management/', '', $request->path());
        
        // Debugging
        Log::info('Current Path: ' . $currentPath);
        Log::info('Full Path: ' . $request->path());
        Log::info('User ID: ' . $user->user_id);
        
        // Cari menu berdasarkan URL
        $menu = WebMenuModel::where('wm_menu_url', $currentPath)
            ->orWhere('wm_menu_url', $request->path())
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->first();
        
        // Jika tidak ditemukan, izinkan akses
        if (!$menu) {
            Log::warning('Menu tidak ditemukan: ' . $currentPath);
            return $next($request);
        }
        
        // Cek hak akses
        $hakAkses = HakAksesModel::where('ha_pengakses', $user->user_id)
            ->where('fk_web_menu', $menu->web_menu_id)
            ->first();
        
        // Buat prefix untuk kolom hak akses
        $hakField = 'ha_' . $permission;
        
        // Debugging hak akses
        Log::info('Menu ID: ' . $menu->web_menu_id);
        Log::info('Hak Akses Field: ' . $hakField);
        Log::info('Hak Akses: ' . ($hakAkses ? $hakAkses->$hakField : 'Tidak Ada'));
        
        if (!$hakAkses || $hakAkses->$hakField != 1) {
            Log::error('Akses Ditolak: ' . $permission);
            
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