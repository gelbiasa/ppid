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
        $beritaDinamis = BeritaDinamisModel::selectData(10, $search);

        return view('AdminWeb.BeritaDinamis.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'beritaDinamis' => $beritaDinamis,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $beritaDinamis = BeritaDinamisModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb.BeritaDinamis.data', compact('beritaDinamis', 'search'))->render();
        }
        
        return redirect()->route('berita-dinamis.index');
    }
  
    public function addData()
    {
        return view('AdminWeb.BeritaDinamis.create');
    }

    public function createData(Request $request)
    {
        try {
            BeritaDinamisModel::validasiData($request);
            $result = BeritaDinamisModel::createData($request);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(BeritaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat membuat berita dinamis'));
        }
    }

    public function editData($id)
    {
        try {
            $beritaDinamis = BeritaDinamisModel::findOrFail($id);
            
            return view('AdminWeb.BeritaDinamis.update', [
                'beritaDinamis' => $beritaDinamis
            ]);
        } catch (\Exception $e) {
            return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            BeritaDinamisModel::validasiData($request, $id);
            $result = BeritaDinamisModel::updateData($request, $id);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(BeritaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui berita dinamis'));
        }
    }

    public function detailData($id)
    {
        try {
            $beritaDinamis = BeritaDinamisModel::findOrFail($id);
            
            return view('AdminWeb.BeritaDinamis.detail', [
                'beritaDinamis' => $beritaDinamis,
                'title' => 'Detail Berita Dinamis'
            ]);
        } catch (\Exception $e) {
            return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $beritaDinamis = BeritaDinamisModel::findOrFail($id);
                
                return view('AdminWeb.BeritaDinamis.delete', [
                    'beritaDinamis' => $beritaDinamis
                ]);
            } catch (\Exception $e) {
                return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = BeritaDinamisModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(BeritaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat menghapus berita dinamis'));
        }
    }
}