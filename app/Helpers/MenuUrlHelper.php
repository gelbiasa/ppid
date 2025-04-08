<?php

namespace App\Helpers;

use App\Models\Website\WebMenuModel;
use Illuminate\Support\Facades\Cache;

class MenuUrlHelper
{
    public static function getMenuUrl($menuName)
    {
        // Menggunakan cache untuk mengurangi query ke database
        $cacheKey = 'menu_url_' . strtolower(str_replace(' ', '_', $menuName));
        
        return Cache::remember($cacheKey, 60 * 24, function () use ($menuName) {
            $menu = WebMenuModel::where('wm_menu_nama', $menuName)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->first();
                
            return $menu ? $menu->wm_menu_url : null;
        });
    }
    
    public static function getMenuByUrl($url)
    {
        return WebMenuModel::where('wm_menu_url', $url)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->first();
    }
}