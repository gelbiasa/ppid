<?php

namespace App\Http\Controllers\AdminWeb\Footer;

use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class KategoriFooterController extends Controller
{
    use TraitsController;
    // Halaman index kategori footer
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Kategori Footer',
            'list' => ['Home', 'Kategori Footer', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Footer'
        ];

        $activeMenu = 'kategori-footer';

        return view('AdminWeb.KategoriFooter.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    // Endpoint untuk DataTables
    public function list(Request $request)
    {
        $kategoriFooter = KategoriFooterModel::select('*');

        return DataTables::of($kategoriFooter)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<div class="btn-group">';
                $btn .= '<button onclick="modalAction(\'' . url('/adminweb/kategori-footer/' . $row->kategori_footer_id . '/edit') . '\')" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="deleteKategoriFooter(' . $row->kategori_footer_id . ')" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn . '</div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Modal tambah kategori footer
    public function create()
    {
        return view('AdminWeb.KategoriFooter.create');
    }


    // Proses simpan kategori footer
    public function store(Request $request)
    {
        $result = KategoriFooterModel::createData($request);
        return response()->json($result);
    }

    public function update(Request $request, $id)
    {
        $result = KategoriFooterModel::updateData($request, $id);
        return response()->json($result);
    }
    public function edit($id)
    {
        $result = KategoriFooterModel::getEditData($id);

        // Pastikan view bisa mengakses data 'kategori_footer'
        if ($result['success']) {
            return view('AdminWeb.KategoriFooter.edit', [
                'kategoriFooter' => $result['kategori_footer']
            ]);
        }

        // Tangani kasus gagal
        return response()->json($result);
    }
    public function delete($id)
    {
        $result = KategoriFooterModel::deleteData($id);
        return response()->json($result);
    }
}