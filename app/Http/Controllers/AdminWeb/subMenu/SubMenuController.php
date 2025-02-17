<?php

namespace App\Http\Controllers\AdminWeb\submenu;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Website\WebMenuModel;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SubMenuController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Sub Menu',
            'list' => ['Home', 'Sub Menu'],
        ];

        $page = (object)[
            'title' => 'Daftar Sub Menu dalam Sistem'
        ];

        $activeMenu = 'submenu';

        return view('adminweb.submenu.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $submenu = WebMenuModel::whereNotNull('wm_parent_id')
            ->with('parentMenu') // Assuming you have this relationship defined
            ->select([
                'web_menu_id',
                'wm_parent_id',
                'wm_menu_nama',
                'wm_menu_url',
                'wm_status_menu',
                'created_by',
                'created_at'
            ])
            ->orderBy('created_at', 'desc');

        return DataTables::of($submenu)
            ->addIndexColumn()
            ->addColumn('parent_menu', function ($submenu) {
                return $submenu->parentMenu ? $submenu->parentMenu->wm_menu_nama : '-';
            })
            ->editColumn('wm_status_menu', function ($submenu) {
                return $submenu->wm_status_menu;
            })
            ->editColumn('created_at', function ($submenu) {
                return Carbon::parse($submenu->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i');
            })
            ->addColumn('aksi', function ($submenu) {
                return '<button onclick="modalAction(\'' . url('/adminweb/submenu/' . $submenu->web_menu_id . '/edit_ajax') . '\')" 
                        class="btn btn-warning btn-sm">Edit</button> 
                        <button onclick="modalAction(\'' . url('/adminweb/submenu/' . $submenu->web_menu_id . '/delete_ajax') . '\')" 
                        class="btn btn-danger btn-sm">Hapus</button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $subMenus = WebMenuModel::whereNull('wm_parent_id')
            ->where('wm_status_menu', 'aktif')
            ->get();
        return view('adminweb.submenu.create_ajax', compact('subMenus'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'wm_parent_id' => 'required|exists:web_menu,web_menu_id',
                'wm_menu_nama' => 'required|string|max:60',
                'wm_status_menu' => 'required|in:aktif,nonaktif',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                $orderNumber = WebMenuModel::where('wm_parent_id', $request->wm_parent_id)->count() + 1;

                WebMenuModel::create([
                    'wm_menu_nama' => $request->wm_menu_nama,
                    'wm_menu_url' => Str::slug($request->wm_menu_nama),
                    'wm_parent_id' => $request->wm_parent_id,
                    'wm_urutan_menu' => $orderNumber,
                    'wm_status_menu' => $request->wm_status_menu,
                    'created_by' => session('alias'),
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Sub menu berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                Log::error('Error: ' . $e->getMessage());

                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect('/dashboardAdmin');
    }
    public function edit_ajax(int $id)
    {
        try {
            $submenu = WebMenuModel::findOrFail($id);
            $subMenus = WebMenuModel::whereNull('wm_parent_id')
                ->where('wm_status_menu', 'aktif')
                ->get();

            return view('adminweb.submenu.edit_ajax', compact('submenu', 'subMenus'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data sub menu tidak ditemukan'
            ], 404);
        }
    }

    public function update_ajax(Request $request, int $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'wm_parent_id' => 'required|exists:web_menu,web_menu_id',
                'wm_menu_nama' => 'required|string|max:60',
                'wm_status_menu' => 'required|in:aktif,nonaktif',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                $submenu = WebMenuModel::findOrFail($id);

                $submenu->update([
                    'wm_menu_nama' => $request->wm_menu_nama,
                    'wm_menu_url' => Str::slug($request->wm_menu_nama),
                    'wm_parent_id' => $request->wm_parent_id,
                    'wm_status_menu' => $request->wm_status_menu,
                    'updated_by' => session('alias'),
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Sub menu berhasil diperbarui',
                ]);
            } catch (\Exception $e) {
                Log::error('Error: ' . $e->getMessage());

                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect('/dashboardAdmin');
    }

    public function confirm_ajax(int $id)
    {
        $submenu = WebMenuModel::with('parentMenu')->find($id);
        
        if (!$submenu) {
            return response()->json([
                'status' => false,
                'message' => 'Data submenu tidak ditemukan'
            ], 404);
        }
    
        return view('adminweb.submenu.confirm_ajax', ['submenu' => $submenu]);
    }
    

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $submenu = WebMenuModel::find($id);

            if (!$submenu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

            try {
                $submenu->update([
                    'deleted_by' => session('alias'),
                    'isDeleted' => 1
                ]);

                $submenu->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Submenu berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                Log::error('Error saat menghapus submenu: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/dashboardAdmin');
    }
}