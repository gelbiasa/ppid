<?php

namespace App\Http\Controllers\AdminWeb\MediaDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\LandingPage\MediaDinamis\MediaDinamisModel;
use App\Models\Website\LandingPage\MediaDinamis\DetailMediaDinamisModel;

class DetailMediaDinamisController extends Controller
{
    use TraitsController;
    
    public $breadcrumb = 'Pengaturan Detail Media Dinamis';
    public $pagename = 'AdminWeb/DetailMediaDinamis';

 
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Detail Media Dinamis',
            'list' => ['Home', 'Media Dinamis', 'Detail Media']
        ];

        $page = (object) [
            'title' => 'Daftar Detail Media Dinamis'
        ];
        
        $activeMenu = 'media-detail';
        
        // Get kategori for filter
        $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
        
        // Get data with filters
        $detailMediaDinamis = DetailMediaDinamisModel::selectData(10, $search, $kategori);

        return view("AdminWeb/DetailMediaDinamis.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'detailMediaDinamis' => $detailMediaDinamis,
            'search' => $search,
            'kategoris' => $kategoris,
            'selectedKategori' => $kategori
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');
        $detailMediaDinamis = DetailMediaDinamisModel::selectData(10, $search, $kategori);
        
        if ($request->ajax()) {
            return view('AdminWeb/DetailMediaDinamis.data', compact('detailMediaDinamis', 'search', 'kategori'))->render();
        }
        
        return redirect()->route('media-detail.index');
    }
  
    public function addData()
    {
        $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
        return view('AdminWeb/DetailMediaDinamis.create', compact('kategoris'));
    }
    public function createData(Request $request)
    {
        try {
            DetailMediaDinamisModel::validasiData($request);
            $result = DetailMediaDinamisModel::createData($request);
    
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail Media Dinamis berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat detail media dinamis');
        }
    }
    
    public function editData($id)
    {
        $detailMediaDinamis = DetailMediaDinamisModel::detailData($id);
        $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
    
        return view("AdminWeb/DetailMediaDinamis.update", [
            'detailMediaDinamis' => $detailMediaDinamis,
            'kategoris' => $kategoris
        ]);
    }
    
    public function updateData(Request $request, $id)
    {
        try {
            
            DetailMediaDinamisModel::validasiData($request);
            $result = DetailMediaDinamisModel::updateData($request, $id);
    
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail Media Dinamis berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui detail media dinamis');
        }
    }
    
    public function detailData($id)
    {
        $detailMediaDinamis = DetailMediaDinamisModel::detailData($id);
        
        
        return view("AdminWeb/DetailMediaDinamis.detail", [
            'detailMediaDinamis' => $detailMediaDinamis,
            'title' => 'Detail Media Dinamis'
        ]);
    }
    
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $detailMediaDinamis = DetailMediaDinamisModel::detailData($id);
            
            return view("AdminWeb/DetailMediaDinamis.delete", [
                'detailMediaDinamis' => $detailMediaDinamis
            ]);
        }
        
        try {
            $result = DetailMediaDinamisModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail Media Dinamis berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus detail media dinamis');
        }
    }
}