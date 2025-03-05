<?php

namespace App\Http\Controllers\AdminWeb\Footer;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\KategoriFooterModel;

class KategoriFooterController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Menu Kategori-Footer';
    public $pagename = 'AdminWeb/kategori-footer';
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

    // 
    public function list(Request $request)
    {
        return KategoriFooterModel::getDataTableList();

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
    public function detail_kategoriFooter($id)
    {
            $result = KategoriFooterModel::getDetailData($id);
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