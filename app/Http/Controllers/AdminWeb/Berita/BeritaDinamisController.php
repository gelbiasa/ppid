<?php

namespace App\Http\Controllers\AdminWeb\Berita;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;
use Illuminate\Validation\ValidationException;

class BeritaDinamisController extends Controller
{
    use TraitsController;
 
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen Berita Dinamis',
            'list' => ['Home', 'Berita Dinamis', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Berita Dinamis'
        ];
        
        $activeMenu = 'berita-dinamis';
        
        // Modify the query to include search functionality
        $kategoriBerita = BeritaDinamisModel::selectData(10, $search);

        return view('AdminWeb.BeritaDinamis.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriBerita' => $kategoriBerita,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriBerita = BeritaDinamisModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/BeritaDinamis.data', compact('kategoriBerita', 'search'))->render();
        }
        
        return redirect()->route('kategori-berita.index');
    }
  
    public function addData()
    {
        return view('AdminWeb/BeritaDinamis.create');
    }

    public function createData(Request $request)
    {
        try {
            BeritaDinamisModel::validasiData($request);
            $result = BeritaDinamisModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori Sub Menu Berita berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat Kategori Sub Menu Berita');
        }
    }

    public function editData($id)
    {
        $kategoriBerita = BeritaDinamisModel::detailData($id);

        return view("AdminWeb/BeritaDinamis.update", [
            'kategoriBerita' => $kategoriBerita
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            BeritaDinamisModel::validasiData($request);
            $result = BeritaDinamisModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori Sub Menu Berita berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Kategori Sub Menu Berita');
        }
    }

    public function detailData($id)
    {
        $kategoriBerita = BeritaDinamisModel::detailData($id);
        
        return view("AdminWeb/BeritaDinamis.detail", [
            'kategoriBerita' => $kategoriBerita,
            'title' => 'Detail Kategori Berita'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $kategoriBerita = BeritaDinamisModel::detailData($id);
            
            return view("AdminWeb/BeritaDinamis.delete", [
                'kategoriBerita' => $kategoriBerita
            ]);
        }
        
        try {
            $result = BeritaDinamisModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori Sub Menu Berita berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Kategori Sub Menu Berita');
        }
    }
}