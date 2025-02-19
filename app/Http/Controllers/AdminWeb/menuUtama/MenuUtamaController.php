<?php

namespace App\Http\Controllers\AdminWeb\menuUtama;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Website\WebMenuModel;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MenuUtamaController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Menu Utama',
            'list' => ['Home', 'Menu Utama'],
        ];

        $page = (object)[
            'title' => 'Daftar Menu Utama dalam Sistem'
        ];

        $activeMenu = 'menuUtama';

        return view('adminweb.menuUtama.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $menu = WebMenuModel::whereNull('wm_parent_id')
            ->select([
                'web_menu_id',
                'wm_menu_nama',
                'wm_menu_url',
                'wm_status_menu',
                'created_by',
                'created_at'
            ])
            ->orderBy('created_at', 'desc');

        return DataTables::of($menu)
            ->addIndexColumn()
            ->editColumn('wm_status_menu', function ($menu) {
                return $menu->wm_status_menu;
            })
            ->editColumn('created_at', function ($menu) {
                return Carbon::parse($menu->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i');
            })
            ->addColumn('aksi', function ($menu) {
                return '<button onclick="modalAction(\'' . url('/adminweb/menu-utama/' . $menu->web_menu_id . '/edit_ajax') . '\')" 
                        class="btn btn-warning btn-sm">Edit</button> 
                        <button onclick="modalAction(\'' . url('/adminweb/menu-utama/' . $menu->web_menu_id . '/delete_ajax') . '\')" 
                        class="btn btn-danger btn-sm">Hapus</button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function create_ajax()
    {
        return view('adminweb.menuUtama.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::createData($request);
            return response()->json($result);
        }
        return redirect('/dashboardAdmin');
    }

    public function edit_ajax(int $id)
    {
        $menu = WebMenuModel::find($id);

        if (!$menu) {
            return view('adminweb.menuUtama.edit_ajax', ['menu' => null]);
        }

        return view('adminweb.menuUtama.edit_ajax', ['menu' => $menu]);
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect('/dashboardAdmin');
    }

    public function confirm_ajax(int $id)
    {
        $menu = WebMenuModel::find($id);
        return view('adminweb.menuUtama.confirm_ajax', ['menu' => $menu]);
    }
    
    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebMenuModel::deleteData($id);
            return response()->json($result);
        }
        return redirect('/dashboardAdmin');
    }
}
