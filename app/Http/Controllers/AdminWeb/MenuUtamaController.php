<?php

namespace App\Http\Controllers\AdminWeb;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Website\WebMenuModel;
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
            ]);

            return DataTables::of($menu)
            ->addIndexColumn()
            ->editColumn('wm_status_menu', function ($menu) {
                return $menu->wm_status_menu; // Langsung kembalikan nilai dari database
            })
            ->editColumn('created_at', function ($menu) {
                return $menu->created_at->format('d-m-Y H:i'); // Format tanggal lebih rapi
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
            // Validasi input form
            $rules = [
                'wm_menu_nama' => 'required|string|max:60',
                'wm_status_menu' => 'required|in:aktif,nonaktif', // Validasi enum untuk wm_status_menu
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
                // Menghitung wm_urutan_menu (orderNumber)
                $orderNumber = WebMenuModel::whereNull('wm_parent_id')->count() + 1;
    
                // Memastikan wm_status_menu diubah menjadi string enum yang valid ('aktif' / 'nonaktif')
                $statusMenu = $request->wm_status_menu; // Nilai sudah dalam format enum 'aktif' atau 'nonaktif'
    
                // Menyimpan data menu ke database
                WebMenuModel::create([
                    'wm_menu_nama' => $request->wm_menu_nama,
                    'wm_menu_url' => Str::slug($request->wm_menu_nama), // Menyimpan slug otomatis
                    'wm_parent_id' => null,
                    'wm_urutan_menu' => $orderNumber,
                    'wm_status_menu' => $statusMenu,  // Pastikan ini sesuai dengan enum
                    'created_by' => auth()->id(),  // Jika diperlukan, simpan ID pengguna yang membuat
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Menu utama berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                // Menyimpan log kesalahan jika terjadi exception
                Log::error('Error: ' . $e->getMessage());
    
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ]);
            }
        }
    
        return redirect('/dashboardAdmin');
    }
}