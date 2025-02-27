<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Website\WebMenuModel;
use App\Http\Controllers\Api\BaseApiController;

class PublicMenuController extends BaseApiController
{
  
    /**
     * Mendapatkan daftar menu publik dalam format hierarki.
     */
    public function getPublicMenus()
    {
        return $this->execute(
            function() {
                $menus = WebMenuModel::selectData();
                return $this->buildMenuTree($menus);
            },
            'menu' // hanya penanda data yang akan diambil
        );
    }

    /**
     * Mengubah daftar menu menjadi struktur hierarki (tree).
     */
    private function buildMenuTree(array $menus, $parentId = null): array
    {
        $tree = [];

        foreach ($menus as $menu) {
            if ($menu['wm_parent_id'] == $parentId) {
                // Rekursi untuk mencari anak dari menu saat ini
                $children = $this->buildMenuTree($menus, $menu['id']);
                if (!empty($children)) {
                    $menu['children'] = $children;
                }
                $tree[] = $menu;
            }
        }

        return $tree;
    }
}