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
                    'wm_status_menu' => $statusMenu,  // memastikan ini sesuai dengan enum
                    'created_by' => auth()->id(),  //  simpan ID pengguna yang membuat
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
    public function edit_ajax(string $id)
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
        $rules = [
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
            $menu = WebMenuModel::findOrFail($id); // Gunakan findOrFail untuk menghindari null return

            $menu->update([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_status_menu' => $request->wm_status_menu,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Menu utama berhasil diperbarui',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Menu tidak ditemukan',
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
public function confirm_ajax(string $id)
{
    $menu = WebMenuModel::find($id);
    return view('adminweb.menuUtama.confirm_ajax', ['menu' => $menu]);
}
public function delete_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {
        $menu = WebMenuModel::find($id);

        if (!$menu) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Periksa apakah menu memiliki submenu
        $hasSubMenu = WebMenuModel::where('wm_parent_id', $id)->exists();

        if ($hasSubMenu) {
            return response()->json([
                'status' => false,
                'message' => 'Menu tidak bisa dihapus karena memiliki submenu di dalamnya'
            ]);
        }

        // Hapus menu jika tidak memiliki submenu
        try {
            $menu->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus menu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    return redirect('/dashboardAdmin');
}

}