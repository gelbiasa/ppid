<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveMenu
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        
        // Tentukan $activeMenu berdasarkan path
        $activeMenu = null;
        
        // Dashboard
        if (preg_match('/^dashboard(ADM|SAR|RPN|MPU|VFR)$/i', $path)) {
            $activeMenu = 'dashboard';
            $breadcrumbTitle = 'Dashboard';
            $breadcrumbList = ['Home', 'Dashboard'];
        }
        // Profile
        elseif (strpos($path, 'profile') !== false) {
            $activeMenu = 'profile';
            $breadcrumbTitle = 'Profil Pengguna';
            $breadcrumbList = ['Home', 'Profil'];
        }
        // Notifikasi
        elseif (strpos($path, 'Notifikasi/NotifAdmin') !== false) {
            $activeMenu = 'notifikasi';
            $breadcrumbTitle = 'Notifikasi Admin';
            $breadcrumbList = ['Home', 'Notifikasi'];
        }
        // KategoriForm
        elseif (strpos($path, 'SistemInformasi/KategoriForm') !== false) {
            $activeMenu = 'kategoriform';
            $breadcrumbTitle = 'Pengaturan Kategori Form';
            $breadcrumbList = ['Home', 'Sistem Informasi', 'Kategori Form'];
        }
        // Management Level
        elseif (strpos($path, 'ManagePengguna') !== false) {
            $activeMenu = 'managementlevel';
            $breadcrumbTitle = 'Pengaturan Level';
            $breadcrumbList = ['Home', 'Manage Pengguna', 'Level'];
        }
        // Hak Akses
        elseif (strpos($path, 'HakAkses') !== false) {
            $activeMenu = 'hakakses';
            $breadcrumbTitle = 'Pengaturan Hak Akses';
            $breadcrumbList = ['Home', 'Hak Akses'];
        }
        // Menu lainnya sesuai dengan format di MenuHelper
        // ...
        
        // Default jika tidak ada yang cocok
        if (!$activeMenu) {
            // Coba deteksi dari bagian terakhir URL (akhir path)
            $segments = explode('/', $path);
            $lastSegment = end($segments);
            $activeMenu = strtolower($lastSegment);
            $breadcrumbTitle = ucfirst($lastSegment);
            $breadcrumbList = ['Home', ucfirst($lastSegment)];
        }
        
        // Tambahkan $activeMenu ke semua view
        view()->share('activeMenu', $activeMenu);
        
        // Tambahkan $breadcrumb ke semua view jika belum ada
        if (!view()->shared('breadcrumb')) {
            $breadcrumb = (object) [
                'title' => $breadcrumbTitle ?? 'Halaman',
                'list' => $breadcrumbList ?? ['Home']
            ];
            view()->share('breadcrumb', $breadcrumb);
        }
        
        // Tambahkan $page ke semua view jika belum ada
        if (!view()->shared('page')) {
            $page = (object) [
                'title' => $breadcrumbTitle ?? 'Halaman'
            ];
            view()->share('page', $page);
        }
        
        return $next($request);
    }
}