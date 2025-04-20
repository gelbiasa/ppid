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

    public $breadcrumb = 'Pengaturan Kategori Akses';
    public $pagename = 'AdminWeb/KategoriAkses';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Kategori Akses',
            'list' => ['Home', 'Pengaturan Kategori Akses']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Akses'
        ];
        
        $activeMenu = 'kategori-akses';
        
        $kategoriAkses = KategoriAksesModel::selectData(10, $search);

        return view("AdminWeb/KategoriAkses.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page, 
            'activeMenu' => $activeMenu,
            'kategoriAkses' => $kategoriAkses,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriAkses = KategoriAksesModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/KategoriAkses.data', compact('kategoriAkses', 'search'))->render();
        }
        
        return redirect()->route('kategori-akses.index');
    }

    public function addData()
    {
        return view("AdminWeb/KategoriAkses.create");
    }

    public function createData(Request $request)
    {
        
        try {
            KategoriAksesModel::validasiData($request);
            $result = KategoriAksesModel::createData($request);
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori akses berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat kategori akses');
        }
    }

    public function editData($id)
    {
        try {
            $kategoriAkses = KategoriAksesModel::findOrFail($id);
            
            return view("AdminWeb/KategoriAkses.update", [
                'kategoriAkses' => $kategoriAkses
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            KategoriAksesModel::validasiData($request, $id);
            $result = KategoriAksesModel::updateData($request, $id);
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori akses berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui kategori akses');
        }
    }

    public function detailData($id)
    {
        try {
            $kategoriAkses = KategoriAksesModel::findOrFail($id);
            
            return view("AdminWeb/KategoriAkses.detail", [
                'kategoriAkses' => $kategoriAkses,
                'title' => 'Detail Kategori Akses'
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat mengambil detail');
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $kategoriAkses = KategoriAksesModel::findOrFail($id);
                
                return view("AdminWeb/KategoriAkses.delete", [
                    'kategoriAkses' => $kategoriAkses
                ]);
            } catch (\Exception $e) {
                return $this->jsonError($e, 'Terjadi kesalahan saat mengambil data');
            }
        }
        
        try {
            $result = KategoriAksesModel::deleteData($id);
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori akses berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus kategori akses');
        }
    }
}