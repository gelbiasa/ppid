<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Website\WebMenuModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthMenuController extends Controller
{
    public function getMenus(Request $request): JsonResponse
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }

            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Query tanpa pengecekan roles
            $menus = WebMenuModel::query()
                ->select([
                    'web_menu_id',
                    'wm_menu_nama as name',
                    'wm_menu_url as url',
                    'wm_parent_id as parent_id',
                    'wm_urutan_menu as order'
                ])
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->orderBy('wm_urutan_menu')
                ->get();

            $menuArray = $menus->map(function ($menu) {
                return [
                    'id' => $menu->web_menu_id,
                    'name' => $menu->name,
                    'url' => $menu->url,
                    'parent_id' => $menu->parent_id,
                    'order' => $menu->order
                ];
            })->toArray();

            $menuTree = $this->buildMenuTree($menuArray);

            return response()->json([
                'success' => true,
                'message' => 'Data menu berhasil diambil',
                'data' => $menuTree
            ]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token telah kadaluarsa'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Menu fetch error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data menu',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function buildMenuTree(array $menus, $parentId = null): array
    {
        $tree = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parentId) {
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