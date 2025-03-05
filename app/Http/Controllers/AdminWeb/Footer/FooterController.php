<?php

namespace App\Http\Controllers\AdminWeb\Footer;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\FooterModel;
use App\Models\Website\Footer\KategoriFooterModel;

class FooterController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Menu Footer';
    public $pagename = 'AdminWeb/footer';
    // Halaman index footer
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Footer',
            'list' => ['Home', 'Footer', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Footer'
        ];
        $activeMenu = 'footer';

        $kategoriFooters = KategoriFooterModel::all();

        return view('AdminWeb.Footer.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriFooters' => $kategoriFooters
        ]);
    }

    // Endpoint untuk DataTables
    public function list(Request $request)
    {
        $footers = FooterModel::with('kategoriFooter')
            ->select('t_footer.*');

        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori') && $request->kategori) {
            $footers->where('fk_m_kategori_footer', $request->kategori);
        }

        return DataTables::of($footers)
            ->addIndexColumn()
            ->addColumn('aksi', function($row) {
                $btn = '<div class="btn-group">';
                $btn .= '<button onclick="modalAction(\''.url('/adminweb/footer/'.$row->footer_id.'/edit').'\')" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="deleteFooter('.$row->footer_id.')" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn . '</div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Modal tambah footer
    public function create()
    {
        $kategoriFooters = KategoriFooterModel::all();
        return view('AdminWeb.Footer.create', compact('kategoriFooters'));
    }

    // Proses simpan footer
    public function store(Request $request)
    {
        try {
            $footer = FooterModel::createData($request);
            return response()->json([
                'success' => true, 
                'message' => 'Footer berhasil ditambahkan',
                'data' => $footer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Modal edit footer
    public function edit($id)
    {
        $footer = FooterModel::findOrFail($id);
        $kategoriFooters = KategoriFooterModel::all();
        return view('AdminWeb.Footer.edit', compact('footer', 'kategoriFooters'));
    }

    // Proses update footer
    public function update(Request $request, $id)
    {
        try {
            $footer = FooterModel::updateData($request, $id);
            return response()->json([
                'success' => true, 
                'message' => 'Footer berhasil diperbarui',
                'data' => $footer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Proses hapus footer
    public function delete($id)
    {
        try {
            FooterModel::deleteData($id);
            return response()->json([
                'success' => true, 
                'message' => 'Footer berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}