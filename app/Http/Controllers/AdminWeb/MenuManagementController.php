<?php

namespace App\Http\Controllers\AdminWeb;

use App\Http\Controllers\Controller;
use App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuManagementController extends Controller
{
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
            
            // Get menus with their children, ordering by menu order
            $menus = WebMenuModel::with(['children' => function ($query) {
                    $query->orderBy('wm_urutan_menu');
                }])
                ->whereNull('wm_parent_id')
                ->where('isDeleted', 0)
                ->orderBy('wm_urutan_menu')
                ->get();

            return view('adminweb.MenuManagement.index', compact('breadcrumb', 'page', 'menus', 'activeMenu'));
        } catch (\Exception $e) {
            Log::error('Error in menu management index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading menu management page');
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'wm_menu_nama' => 'required|string|max:60',
                'wm_parent_id' => 'nullable|exists:web_menu,web_menu_id',
                'wm_status_menu' => 'required|in:aktif,nonaktif',
            ], [
                'wm_menu_nama.required' => 'Menu name is required',
                'wm_menu_nama.max' => 'Menu name cannot exceed 60 characters',
                'wm_parent_id.exists' => 'Selected parent menu does not exist',
                'wm_status_menu.required' => 'Menu status is required',
                'wm_status_menu.in' => 'Invalid menu status',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the next order number for that level
            $orderNumber = WebMenuModel::where('wm_parent_id', $request->wm_parent_id)
                ->where('isDeleted', 0)
                ->count() + 1;

            // Create the menu
            $menu = WebMenuModel::create([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_parent_id' => $request->wm_parent_id,
                'wm_urutan_menu' => $orderNumber,
                'wm_status_menu' => $request->wm_status_menu,
                'created_by' => session('alias'),
                'isDeleted' => 0
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu created successfully',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating menu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error creating menu'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $menu = WebMenuModel::findOrFail($id);
            
            // Get possible parent menus (excluding the current menu and its children)
            $parentMenus = WebMenuModel::whereNull('wm_parent_id')
                ->where('web_menu_id', '!=', $id)
                ->where('isDeleted', 0)
                ->whereNotIn('web_menu_id', function($query) use ($id) {
                    $query->select('web_menu_id')
                        ->from('web_menu')
                        ->where('wm_parent_id', $id);
                })
                ->get();

            return response()->json([
                'status' => true,
                'menu' => $menu,
                'parentMenus' => $parentMenus
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching menu for edit: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching menu details'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'wm_menu_nama' => 'required|string|max:60',
                'wm_parent_id' => 'nullable|exists:web_menu,web_menu_id',
                'wm_status_menu' => 'required|in:aktif,nonaktif',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $menu = WebMenuModel::findOrFail($id);

            // Check if trying to set a child as parent
            if ($request->wm_parent_id) {
                $isChild = WebMenuModel::where('wm_parent_id', $id)
                    ->where('web_menu_id', $request->wm_parent_id)
                    ->exists();
                
                if ($isChild) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Cannot set a child menu as parent'
                    ], 422);
                }
            }

            // Update the menu
            $menu->update([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_parent_id' => $request->wm_parent_id,
                'wm_status_menu' => $request->wm_status_menu,
                'updated_by' => session('alias')
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu updated successfully',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating menu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error updating menu'
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $menu = WebMenuModel::findOrFail($id);
            
            // Check for children
            if ($menu->children()->where('isDeleted', 0)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete menu with active submenu items. Please delete or reassign submenu items first.'
                ], 422);
            }

            // Soft delete the menu
            $menu->update([
                'deleted_by' => session('alias'),
                'isDeleted' => 1,
                'deleted_at' => now()
            ]);

            // Reorder remaining menus in the same level
            $this->reorderMenus($menu->wm_parent_id);
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting menu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error deleting menu'
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->get('data');
            
            foreach ($data as $position => $item) {
                $menu = WebMenuModel::find($item['id']);
                if ($menu) {
                    $menu->update([
                        'wm_parent_id' => $item['parent_id'] ?? null,
                        'wm_urutan_menu' => $position + 1,
                        'updated_by' => session('alias')
                    ]);

                    // If there are children, update their positions
                    if (isset($item['children'])) {
                        foreach ($item['children'] as $childPosition => $child) {
                            $childMenu = WebMenuModel::find($child['id']);
                            if ($childMenu) {
                                $childMenu->update([
                                    'wm_parent_id' => $item['id'],
                                    'wm_urutan_menu' => $childPosition + 1,
                                    'updated_by' => session('alias')
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Menu order updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reordering menu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error reordering menu'
            ], 500);
        }
    }

    private function reorderMenus($parentId = null)
    {
        $menus = WebMenuModel::where('wm_parent_id', $parentId)
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        foreach ($menus as $index => $menu) {
            $menu->update([
                'wm_urutan_menu' => $index + 1
            ]);
        }
    }
}