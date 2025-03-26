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
 
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        

        $breadcrumb = (object) [
            'title' => 'Manajemen Media Dinamis',
            'list' => ['Home', 'Media Dinamis', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Media Dinamis'
        ];
        
        $activeMenu = 'media-dinamis';
        
        // Modify the query to include search functionality
        $mediaDinamis = MediaDinamisModel::selectData(10, $search);

        return view('AdminWeb.MediaDinamis.index', [
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
            return view('AdminWeb.MediaDinamis.data', compact('mediaDinamis', 'search'))->render();
        }
        
        return redirect()->route('media-dinamis.index');
    }
  
    public function addData()
    {
        return view('AdminWeb.MediaDinamis.create');
    }

    public function createData(Request $request)
    {
        try {
            MediaDinamisModel::validasiData($request);
            $result = MediaDinamisModel::createData($request);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(MediaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat membuat media dinamis'));
        }
    }

    public function editData($id)
    {
        try {
            $mediaDinamis = MediaDinamisModel::findOrFail($id);
            
            return view('AdminWeb.MediaDinamis.update', [
                'mediaDinamis' => $mediaDinamis
            ]);
        } catch (\Exception $e) {
            return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            MediaDinamisModel::validasiData($request);
            $result = MediaDinamisModel::updateData($request, $id);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(MediaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui media dinamis'));
        }
    }

    public function detailData($id)
    {
        try {
            $mediaDinamis = MediaDinamisModel::findOrFail($id);
            
            return view('AdminWeb.MediaDinamis.detail', [
                'mediaDinamis' => $mediaDinamis,
                'title' => 'Detail Media Dinamis'
            ]);
        } catch (\Exception $e) {
            return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $mediaDinamis = MediaDinamisModel::findOrFail($id);
                
                return view('AdminWeb.MediaDinamis.delete', [
                    'mediaDinamis' => $mediaDinamis
                ]);
            } catch (\Exception $e) {
                return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = MediaDinamisModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(MediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat menghapus media dinamis'));
        }
    }
}