<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Website\WebMenuModel;

class AuthMenuController extends BaseApiController
{
    
    public function getAuthMenus()
    {
        return $this->eksekusiDenganOtentikasi(
            function() {
                $menus = WebMenuModel::selectData();
                return $menus;
            },
            'menu' // hanya penanda data yang akan diambil
        );
    }
}