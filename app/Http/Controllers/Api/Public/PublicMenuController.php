<?php


use Illuminate\Http\JsonResponse;
use App\Models\Website\WebMenuModel;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\public\ApiController;

class PublicMenuController extends ApiController
{
    public function __construct()
    {
        parent :: __construct();
    }
    /**
     * Mendapatkan daftar menu publik dalam format hierarki.
     */
    public function getPublicMenus(): JsonResponse
    {
        try {
            $menus = WebMenuModel::selectData();
            // Ubah menjadi struktur hierarki
            $menuTree = $this->buildMenuTree($menus);

            return response()->json([
                'status' => true,
                'message' => 'Data menu berhasil diambil.',
                'data' => $menuTree
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data menu. Silakan coba lagi.',
                'error' => $e->getMessage() // Tambahkan untuk debugging jika diperlukan
            ], 500);
        }
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