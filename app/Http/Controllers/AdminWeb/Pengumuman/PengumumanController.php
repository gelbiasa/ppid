<?php

namespace App\Http\Controllers\AdminWeb\Pengumuman;

use App\Http\Controllers\TraitsController;
use App\Models\Website\Publikasi\Pengumuman\PengumumanDinamisModel;
use App\Models\Website\Publikasi\Pengumuman\PengumumanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengumumanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Pengumuman';
    public $pagename = 'AdminWeb/Pengumuman';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Pengumuman',
            'list' => ['Home', 'Website', 'Pengumuman']
        ];

        $page = (object) [
            'title' => 'Daftar Pengumuman'
        ];

        $activeMenu = 'Pengumuman';

        // Gunakan pagination dan pencarian
        $pengumuman = PengumumanModel::selectData(10, $search);

        return view("AdminWeb/Pengumuman.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'pengumuman' => $pengumuman,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pagination dan pencarian
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $pengumuman = PengumumanModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/Pengumuman.data', compact('pengumuman', 'search'))->render();
        }
        
        return redirect()->route('pengumuman.index');
    }

    public function addData()
    {
        $kategoriPengumuman = PengumumanDinamisModel::where('isDeleted', 0)->get();

        return view("AdminWeb/Pengumuman.create", [
            'kategoriPengumuman' => $kategoriPengumuman
        ]);
    }

    public function createData(Request $request)
    {
        try {
            PengumumanModel::validasiData($request);
            $result = PengumumanModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat pengumuman');
        }
    }

    public function editData($id)
    {
        $kategoriPengumuman = PengumumanDinamisModel::where('isDeleted', 0)->get();
        $pengumuman = PengumumanModel::detailData($id);

        return view("AdminWeb/Pengumuman.update", [
            'kategoriPengumuman' => $kategoriPengumuman,
            'pengumuman' => $pengumuman
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            PengumumanModel::validasiData($request);
            $result = PengumumanModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui pengumuman');
        }
    }

    public function detailData($id)
    {
        $pengumuman = PengumumanModel::detailData($id);
        
        return view("AdminWeb/Pengumuman.detail", [
            'pengumuman' => $pengumuman,
            'title' => 'Detail Pengumuman'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $pengumuman = PengumumanModel::detailData($id);
            
            return view("AdminWeb/Pengumuman.delete", [
                'pengumuman' => $pengumuman
            ]);
        }
        
        try {
            $result = PengumumanModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus pengumuman');
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

            $fileName = 'pengumuman/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
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