<?php

namespace App\Http\Controllers\AdminWeb\Footer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Validation\ValidationException;

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

    // Mendapatkan data untuk tabel
    public function getData()
    {
        $result = KategoriFooterModel::selectData();
        $data = [];

        foreach ($result as $key => $kategoriFooter) {
            $row = [];
            $row[] = $key + 1;
            $row[] = $kategoriFooter->kt_footer_kode;
            $row[] = $kategoriFooter->kt_footer_nama;

            $actions = '
            <button class="btn btn-sm btn-warning" onclick="modalAction(\'' . url("adminweb/kategori-footer/editData/{$kategoriFooter->kategori_footer_id}") . '\')">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-sm btn-info" onclick="modalAction(\'' . url("adminweb/kategori-footer/detailData/{$kategoriFooter->kategori_footer_id}") . '\')">
                <i class="fas fa-eye"></i> Detail
            </button>
            <button class="btn btn-sm btn-danger" onclick="modalAction(\'' . url("adminweb/kategori-footer/deleteData/{$kategoriFooter->kategori_footer_id}") . '\')">
                <i class="fas fa-trash"></i> Hapus
            </button>';

            $row[] = $actions;
            $data[] = $row;
        }

        return response()->json(['data' => $data]);
    }

    // Modal tambah kategori footer
    public function addData()
    {
        return view('AdminWeb.KategoriFooter.create');
    }

    // Proses simpan kategori footer
    public function createData(Request $request)
    {
        try {
            KategoriFooterModel::validasiData($request);
            $result = KategoriFooterModel::createData($request);

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KategoriFooterModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat membuat kategori footer'));
        }
    }

    // Halaman edit kategori footer
    public function editData($id)
    {
        try {
            $kategoriFooter = KategoriFooterModel::findOrFail($id);
            return view('AdminWeb.KategoriFooter.update', [
                'kategoriFooter' => $kategoriFooter
            ]);
        } catch (\Exception $e) {
            return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    // Proses update kategori footer
    public function updateData(Request $request, $id)
    {
        try {
            KategoriFooterModel::validasiData($request, $id);
            $result = KategoriFooterModel::updateData($request, $id);

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KategoriFooterModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui kategori footer'));
        }
    }

    // Halaman detail kategori footer
    public function detailData($id)
    {
        try {
            $kategoriFooter = KategoriFooterModel::detailData($id);
            
            return view('AdminWeb.KategoriFooter.detail', [
                'kategoriFooter' => $kategoriFooter,
                'title' => 'Detail Kategori Footer'
            ]);
        } catch (\Exception $e) {
            return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    // Halaman dan proses hapus kategori footer
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $kategoriFooter = KategoriFooterModel::detailData($id);
                
                return view('AdminWeb.KategoriFooter.delete', [
                    'kategoriFooter' => $kategoriFooter
                ]);
            } catch (\Exception $e) {
                return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = KategoriFooterModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(KategoriFooterModel::responFormatError($e, 'Terjadi kesalahan saat menghapus kategori footer'));
        }
    }
}