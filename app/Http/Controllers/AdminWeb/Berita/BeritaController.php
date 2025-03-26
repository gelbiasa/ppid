<?php

namespace App\Http\Controllers\AdminWeb\Berita;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\Publikasi\Berita\BeritaModel;
use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;

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
        
        $activeMenu = 'berita';
        
        $berita = BeritaModel::selectData(10, $search);
        $beritaDinamis = BeritaDinamisModel::where('isDeleted', 0)->get();

        return view('AdminWeb.Berita.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'berita' => $berita,
            'beritaDinamis' => $beritaDinamis,
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

    public function addData()
    {
        $beritaDinamis = BeritaDinamisModel::where('isDeleted', 0)->get();
        return view('AdminWeb.Berita.create', compact('beritaDinamis'));
    }

    public function createData(Request $request)
    {
        try {
            $result = BeritaModel::createData($request);
            
            return response()->json([
                'status' => true,
                'message' => 'Berita berhasil dibuat',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat berita: ' . $e->getMessage()
            ], 500);
        }
    }
    public function detailData($id)
{
    try {
        $berita = BeritaModel::with('BeritaDinamis')->findOrFail($id);
        $beritaDinamis = BeritaDinamisModel::where('isDeleted', 0)->get();
        
        return view('AdminWeb.Berita.detail', [
            'berita' => $berita,
            'beritaDinamis' => $beritaDinamis
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Gagal mengambil detail berita: ' . $e->getMessage()
        ], 500);
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
        return response()->json([
            'status' => false,
            'message' => 'Gagal mengambil data berita: ' . $e->getMessage()
        ], 500);
    }
}

public function updateData(Request $request, $id)
{
    try {
        $result = BeritaModel::updateData($request, $id);
        
        return response()->json([
            'status' => true,
            'message' => 'Berita berhasil diperbarui',
            'data' => $result
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Gagal memperbarui berita: ' . $e->getMessage()
        ], 500);
    }
}

public function deleteData(Request $request, $id)
{
    if ($request->isMethod('get')) {
        try {
            $berita = BeritaModel::with('BeritaDinamis')->findOrFail($id);
            
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

    public function uploadImage(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'berita/' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public', $fileName);
                
                return response()->json([
                    'success' => true,
                    'url' => asset('storage/' . $fileName)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
            ], 500);
        }
    }
}