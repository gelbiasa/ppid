<?php

namespace App\Helpers;

use App\Models\HakAkses\HakAksesModel;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    /**
     * Memeriksa apakah pengguna memiliki akses ke menu
     * 
     * @param string $menuUrl URL menu yang akan diperiksa
     * @return bool true jika pengguna memiliki akses
     */
    public static function userHasAccess($menuUrl)
    {
        $user = Auth::user();
        
        // Super Admin selalu punya akses penuh
        if ($user->level->level_kode === 'SAR') {
            return true;
        }
        
        // Cek hak akses view untuk menu ini
        return HakAksesModel::cekHakAkses($user->user_id, $menuUrl, 'view');
    }
    
    /**
     * Memeriksa apakah menu harus ditampilkan di sidebar
     * Selalu mengembalikan true kecuali untuk kondisi khusus
     * 
     * @param string $menuUrl URL menu yang akan diperiksa
     * @return bool true jika menu harus ditampilkan di sidebar
     */
    public static function shouldShowInSidebar($menuUrl)
    {
        // Selalu tampilkan menu di sidebar, terlepas dari hak akses
        return true;
    }
    
    /**
     * Mendapatkan URL untuk menu berdasarkan kondisi akses
     * 
     * @param string $menuUrl URL asli menu
     * @return string URL yang akan digunakan (original atau javascript void)
     */
    public static function getMenuUrl($menuUrl)
    {
        if (self::userHasAccess($menuUrl)) {
            return url($menuUrl);
        } else {
            // Jika tidak memiliki akses, tetap berikan URL asli
            // Middleware akan menangani pengalihan ke halaman 403
            return url($menuUrl);
        }
    }
}