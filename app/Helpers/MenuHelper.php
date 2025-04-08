<?php

namespace App\Helpers;

use App\Models\HakAkses\HakAksesModel;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{   
    public static function shouldShowInSidebar($menuUrl)
    {
        $user = Auth::user();
        
        // Super Admin selalu melihat semua menu
        if ($user->level->level_kode === 'SAR') {
            return true;
        }
        
        // Cek hak akses menu untuk penampilan di sidebar
        return HakAksesModel::cekHakAksesMenu($user->user_id, $menuUrl);
    }
}