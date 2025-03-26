<?php

namespace App\Http\Controllers\AdminWeb\KategoriAkses;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;
use Illuminate\Validation\ValidationException;

class KategoriAksesController extends Controller
{
    use TraitsController;
 
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen Kategori Akses',
            'list' => ['Home', 'Kategori Akses', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Akses'
        ];
        
        $activeMenu = 'kategori-akses';
        
        // Modify the query to include search functionality
        $kategoriAkses = KategoriAksesModel::selectData(10, $search);

        return view('AdminWeb.KategoriAkses.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page, 
            'activeMenu' => $activeMenu,
            'kategoriAkses' => $kategoriAkses,
            'search' => $search
        ]);
    }

    // Update getData method to support search
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriAkses = KategoriAksesModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb.KategoriAkses.data', compact('kategoriAkses', 'search'))->render();
        }
        
        return redirect()->route('kategori-akses.index');
    }
  
    // Modal tambah kategori akses
    public function addData()
    {
        return view('AdminWeb.KategoriAkses.create');
    }

    // Proses simpan kategori akses
    public function createData(Request $request)
    {
        try {
            KategoriAksesModel::validasiData($request);
            $result = KategoriAksesModel::createData($request);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KategoriAksesModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat membuat kategori akses'));
        }
    }

    // Modal edit kategori akses
    public function editData($id)
    {
        try {
            $kategoriAkses = KategoriAksesModel::findOrFail($id);
            
            return view('AdminWeb.KategoriAkses.update', [
                'kategoriAkses' => $kategoriAkses
            ]);
        } catch (\Exception $e) {
            return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    // Proses update kategori akses
    public function updateData(Request $request, $id)
    {
        try {
            // Pastikan validasi dan update dipanggil dengan benar
            KategoriAksesModel::validasiData($request, $id);
            $result = KategoriAksesModel::updateData($request, $id);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KategoriAksesModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui kategori akses'));
        }
    }
    public function detailData($id)
    {
        try {
            $kategoriAkses = KategoriAksesModel::findOrFail($id);
            
            return view('AdminWeb.KategoriAkses.detail', [
                'kategoriAkses' => $kategoriAkses,
                'title' => 'Detail Kategori Akses'
            ]);
        } catch (\Exception $e) {
            return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    // Halaman dan proses hapus kategori akses
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $kategoriAkses = KategoriAksesModel::findOrFail($id);
                
                return view('AdminWeb.KategoriAkses.delete', [
                    'kategoriAkses' => $kategoriAkses
                ]);
            } catch (\Exception $e) {
                return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = KategoriAksesModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(KategoriAksesModel::responFormatError($e, 'Terjadi kesalahan saat menghapus kategori akses'));
        }
    }
}