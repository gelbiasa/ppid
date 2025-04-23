<?php

namespace App\Http\Controllers\AdminWeb\Berita;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\Publikasi\Berita\BeritaModel;
use App\Models\Website\Publikasi\Berita\BeritaDinamisModel;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Pengaturan Berita';
    public $pagename = 'AdminWeb/Berita';
    
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
        
        $activeMenu = 'detailberita';
        
        $detailBerita = BeritaModel::selectData(5, $search);
        $kategoriBerita = BeritaDinamisModel::where('isDeleted', 0)->get();

        return view('AdminWeb/Berita.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'detailBerita' => $detailBerita,
            'kategoriBerita' => $kategoriBerita,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $detailBerita = BeritaModel::selectData(5, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/Berita.data', compact('detailBerita', 'search'))->render();
        }
        
        return redirect()->route('detail-berita.index');
    }

    public function addData()
    {
        $kategoriBerita = BeritaDinamisModel::where('isDeleted', 0)->get();
        return view("AdminWeb/Berita.create", [
            'kategoriBerita' => $kategoriBerita
        ]);
    }

    public function createData(Request $request)
    {
        try {
            BeritaModel::validasiData($request);
            $result = BeritaModel::createData($request);
            
            return $this->jsonSuccess(
                $result,
                'Berita berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal membuat berita');
        }
    }

    public function editData($id)
    {
        try {
            $kategoriBerita = BeritaDinamisModel::where('isDeleted', 0)->get();
            $detailBerita = BeritaModel::detailData($id);
            
            return view('AdminWeb/Berita.update', [
                'kategoriBerita' => $kategoriBerita,
                'detailBerita' => $detailBerita
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal mengambil data berita');
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            BeritaModel::validasiData($request);
            $result = BeritaModel::updateData($request, $id);
            
            return $this->jsonSuccess(
                $result,
                'Berita berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memperbarui berita');
        }
    }

    public function detailData($id)
    {
        try {
            $detailBerita = BeritaModel::detailData($id);

            return view("AdminWeb/Berita.detail", [
                'detailBerita' => $detailBerita,
                'title' => 'Detail Berita'
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal mengambil detail Berita');
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $detailBerita = BeritaModel::detailData($id);
                
                return view('AdminWeb/Berita.delete', [
                    'detailBerita' => $detailBerita
                ]);
            } catch (\Exception $e) {
                return $this->jsonError($e, 'Terjadi kesalahan saat mengambil data');
            }
        }
        
        try {
            $result = BeritaModel::deleteData($id);
            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Berita berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus berita');
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $file = $request->file('image');
            
            if (!$file) {
                return $this->jsonError(
                    new \Exception('Tidak ada file yang diunggah'), 
                    '', 
                    400
                );
            }

            $fileName = 'berita/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $fileName);
            
            return $this->jsonSuccess(
                ['url' => asset('storage/' . $fileName)], 
                'Gambar berhasil diunggah'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function removeImage(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|string'
            ]);
            
            $imageUrl = $request->input('url');
            
            // Extract filename dari full URL
            $pathInfo = parse_url($imageUrl);
            $path = $pathInfo['path'] ?? '';
            $storagePath = str_replace('/storage/', '', $path);
            
            if (!empty($storagePath)) {
                // Logika untuk menghapus file
                $filePath = storage_path('app/public/' . $storagePath);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                return $this->jsonSuccess(
                    null, 
                    'Gambar berhasil dihapus'
                );
            } else {
                return $this->jsonError(
                    new \Exception('Path gambar tidak valid'), 
                    '', 
                    400
                );
            }
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }
}