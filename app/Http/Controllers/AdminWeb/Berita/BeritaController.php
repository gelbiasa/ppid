<?php

namespace App\Http\Controllers\AdminWeb\Berita;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Publikasi\Berita\BeritaModel;
use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;
use Illuminate\Validation\ValidationException;

class BeritaController extends Controller
{
    use TraitsController;
 
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen Berita',
            'list' => ['Home', 'Berita', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Berita'
        ];
        
        $activeMenu = 'upload-berita';
        
        $berita = BeritaModel::selectData(10, $search);

        return view('AdminWeb.Berita.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'berita' => $berita,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $berita = BeritaModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb.Berita.data', compact('berita', 'search'))->render();
        }
        
        return redirect()->route('berita.index');
    }
  
    public function addData(Request $request)
    {
        $type = $request->query('type', 'file');
        $beritaDinamis = BeritaDinamisModel::where('isDeleted', 0)->get();

        return view('AdminWeb.Berita.create', [
            'type' => $type,
            'beritaDinamis' => $beritaDinamis
        ]);
    }

    public function createData(Request $request)
    {
        try {
            BeritaModel::validasiData($request);
            $result = BeritaModel::createData($request);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(BeritaModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat membuat berita'));
        }
    }

    public function editData($id)
    {
        try {
            $berita = BeritaModel::findOrFail($id);
            $beritaDinamis = BeritaDinamisModel::where('isDeleted', 0)->get();
            
            return view('AdminWeb.Berita.update', [
                'berita' => $berita,
                'beritaDinamis' => $beritaDinamis
            ]);
        } catch (\Exception $e) {
            return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            BeritaModel::validasiData($request, $id);
            $result = BeritaModel::updateData($request, $id);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(BeritaModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui berita'));
        }
    }

    public function detailData($id)
    {
        try {
            $berita = BeritaModel::with(['BeritaDinamis', 'uploadBerita'])->findOrFail($id);
            
            return view('AdminWeb.Berita.detail', [
                'berita' => $berita,
                'title' => 'Detail Berita'
            ]);
        } catch (\Exception $e) {
            return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $berita = BeritaModel::findOrFail($id);
                
                return view('AdminWeb.Berita.delete', [
                    'berita' => $berita
                ]);
            } catch (\Exception $e) {
                return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = BeritaModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(BeritaModel::responFormatError($e, 'Terjadi kesalahan saat menghapus berita'));
        }
    }

    // Tambahan method untuk upload dan remove image
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2560',
            ]);

            $file = $request->file('image');
            
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file yang diunggah'
                ], 400);
            }

            $url = BeritaModel::uploadImage($file);
            
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar'
            ], 500);
        }
    }

    public function removeImage(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|string'
            ]);
            
            $imageUrl = $request->input('url');
            
            $result = BeritaModel::removeImage($imageUrl);
            
            return response()->json([
                'success' => $result,
                'message' => $result ? 'Gambar berhasil dihapus' : 'Gagal menghapus gambar'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gambar'
            ], 500);
        }
    }
}