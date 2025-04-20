<?php

namespace App\Http\Controllers\AdminWeb\MediaDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\LandingPage\MediaDinamis\MediaDinamisModel;
use Illuminate\Validation\ValidationException;

class MediaDinamisController extends Controller
{
    use TraitsController;
    
    public $breadcrumb = 'Pengaturan Media Dinamis';
    public $pagename = 'AdminWeb/MediaDinamis';

 
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        

        $breadcrumb = (object) [
            'title' => 'Pengaturan Media Dinamis',
            'list' => ['Home', 'Pengaturan Media Dinamis']
        ];

        $page = (object) [
            'title' => 'Daftar Media Dinamis'
        ];
        
        $activeMenu = 'media-dinamis';
        
        $mediaDinamis = MediaDinamisModel::selectData(10, $search);

        return view("AdminWeb/MediaDinamis.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'mediaDinamis' => $mediaDinamis,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $mediaDinamis = MediaDinamisModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/MediaDinamis.data', compact('mediaDinamis', 'search'))->render();
        }
        
        return redirect()->route('media-dinamis.index');
    }
  
    public function addData()
    {
        return view("AdminWeb/MediaDinamis.create");
    }

    public function createData(Request $request)
    {
        try {
            MediaDinamisModel::validasiData($request);
            $result = MediaDinamisModel::createData($request);
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Media Dinamis berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat Media Dinamis');
        }
    }

    public function editData($id)
    {
            $mediaDinamis = MediaDinamisModel::detailData($id);
            
            return view("AdminWeb/MediaDinamis.update", [
                'mediaDinamis' => $mediaDinamis
            ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            MediaDinamisModel::validasiData($request);
            $result = MediaDinamisModel::updateData($request, $id);
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Media Dinamis berhasil diperbarui'
        );
    } catch (ValidationException $e) {
        return $this->jsonValidationError($e);
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Media Dinamis');
    }
    }

    public function detailData($id)
    {
            $mediaDinamis = MediaDinamisModel::detailData($id);
            
            return view("AdminWeb/MediaDinamis.detail", [
                'mediaDinamis' => $mediaDinamis,
                'title' => 'Detail Media Dinamis'
            ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
                $mediaDinamis = MediaDinamisModel::detailData($id);
                
                return view("AdminWeb/MediaDinamis.delete", [
                    'mediaDinamis' => $mediaDinamis
                ]);
        
        }
        try {
            $result =MediaDinamisModel::deleteData($id);
          // Periksa apakah operasi berhasil
          if ($result['success'] === false) {
            return $this->jsonError(new \Exception($result['message']), $result['message']);
        }
        
        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Media Dinamis berhasil dihapus'
        );
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Media Dinamis');
    }
}
    
}