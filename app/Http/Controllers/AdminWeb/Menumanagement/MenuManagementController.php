<?php

namespace App\Http\Controllers\AdminWeb\MenuManagement;

use App\Http\Controllers\TraitsController;
use App\Models\LevelModel;
use App\Models\Website\WebMenuModel;
use App\Models\Website\WebMenuUrlModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MenuManagementController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Menu Management';
    public $pagename = 'AdminWeb/MenuManagement';
    
    public function index()
    {
        try {
            $breadcrumb = (object)[
                'title' => 'Menu Management',
                'list' => ['Home', 'Menu Management'],
            ];

            $page = (object)[
                'title' => 'Menu Management System'
            ];

            $activeMenu = 'menumanagement';

            // Dapatkan semua level dari database
            $levels = LevelModel::where('isDeleted', 0)->get();

            // Gunakan nama level sebagai daftar jenis menu
            $jenisMenuList = $levels->pluck('level_nama', 'level_kode')->toArray();

            // Dapatkan menu dikelompokkan berdasarkan level
            $menusByJenis = [];
            foreach ($levels as $level) {
                $levelId = $level->level_id;
                $menusByJenis[$level->level_kode] = [
                    'nama' => $level->level_nama,
                    'menus' => WebMenuModel::where('fk_m_level', $levelId)
                        ->whereNull('wm_parent_id')
                        ->where('isDeleted', 0)
                        ->orderBy('wm_urutan_menu')
                        ->with(['children' => function ($query) use ($levelId) {
                            $query->where('fk_m_level', $levelId)
                                ->where('isDeleted', 0)
                                ->orderBy('wm_urutan_menu');
                        }, 'WebMenuGlobal', 'Level'])
                        ->get()
                ];
            }

            // Untuk dropdown di form
            $menus = WebMenuModel::getMenusWithChildren();

            // Dapatkan daftar URL untuk dropdown
            $menuUrls = WebMenuUrlModel::ppidOnly()->where('isDeleted', 0)->get();

            return view('adminweb.MenuManagement.index', compact(
                'breadcrumb',
                'page',
                'menus',
                'activeMenu',
                'menusByJenis',
                'jenisMenuList',
                'levels',
                'menuUrls'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading menu management page: ' . $e->getMessage());
        }
    }

    // Method lain tetap sama
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::createData($request);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::getEditData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::deleteData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function detail_menu($id)
    {
        if (request()->ajax()) {
            $result = WebMenuModel::getDetailData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function reorder(Request $request)
    {
        if ($request->ajax()) {
            $result = WebMenuModel::reorderMenus($request->get('data'));
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function getParentMenus($levelId)
    {
        $parentMenus = WebMenuModel::getParentMenusByLevel($levelId);
        return response()->json([
            'success' => true,
            'parentMenus' => $parentMenus
        ]);
    }
}