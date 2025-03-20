<?php

namespace App\Http\Controllers\SistemInformasi\KetentuanPelaporan;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use App\Models\SistemInformasi\KetentuanPelaporan\KetentuanPelaporanModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KetentuanPelaporanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Ketentuan Pelaporan';
    public $pagename = 'SistemInformasi/KetentuanPelaporan';

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Pengaturan Ketentuan Pelaporan',
            'list' => ['Home', 'Pengaturan Ketentuan Pelaporan']
        ];

        $page = (object) [
            'title' => 'Daftar Ketentuan Pelaporan'
        ];

        $activeMenu = 'KetentuanPelaporan';

        return view("SistemInformasi/KetentuanPelaporan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function getData()
    {
        $result = KetentuanPelaporanModel::selectData();
        $data = [];
        
        foreach ($result as $key => $ketentuanPelaporan) {
            $row = [];
            $row[] = $key + 1;
            $row[] = $ketentuanPelaporan->PelaporanKategoriForm->kf_nama ?? '-';
            $row[] = $ketentuanPelaporan->kp_judul;
            
            $row[] = $this->generateActionButtons(
                'SistemInformasi/KetentuanPelaporan', 
                $ketentuanPelaporan->ketentuan_pelaporan_id
            );
            $data[] = $row;
        }
        
        return response()->json(['data' => $data]);
    }

    public function addData()
    {
        $kategoriForms = KategoriFormModel::where('isDeleted', 0)->get();

        return view("SistemInformasi/KetentuanPelaporan.create", [
            'kategoriForms' => $kategoriForms
        ]);
    }

    public function createData(Request $request)
    {
        try {
            KetentuanPelaporanModel::validasiData($request);
            $result = KetentuanPelaporanModel::createData($request);
            
            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Ketentuan pelaporan berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat ketentuan pelaporan');
        }
    }

    public function editData($id)
    {
        $kategoriForms = KategoriFormModel::where('isDeleted', 0)->get();
        $ketentuanPelaporan = KetentuanPelaporanModel::findOrFail($id);

        return view("SistemInformasi/KetentuanPelaporan.update", [
            'kategoriForms' => $kategoriForms,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            KetentuanPelaporanModel::validasiData($request);
            $result = KetentuanPelaporanModel::updateData($request, $id);
            
            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Ketentuan pelaporan berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui ketentuan pelaporan');
        }
    }

    public function detailData($id)
    {
        $ketentuanPelaporan = KetentuanPelaporanModel::with('PelaporanKategoriForm')->findOrFail($id);
        
        return view("SistemInformasi/KetentuanPelaporan.detail", [
            'ketentuanPelaporan' => $ketentuanPelaporan,
            'title' => 'Detail Ketentuan Pelaporan'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ketentuanPelaporan = KetentuanPelaporanModel::with('PelaporanKategoriForm')->findOrFail($id);
            
            return view("SistemInformasi/KetentuanPelaporan.delete", [
                'ketentuanPelaporan' => $ketentuanPelaporan
            ]);
        }
        
        try {
            $result = KetentuanPelaporanModel::deleteData($id);
            
            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Ketentuan pelaporan berhasil dihapus'
            );
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus ketentuan pelaporan');
        }
    }

    // Method untuk upload gambar
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $file = $request->file('image');
            
            if (!$file) {
                // Menggunakan method jsonError dari BaseControllerFunction
                return $this->jsonError(
                    new \Exception('Tidak ada file yang diunggah'), 
                    '', 
                    400
                );
            }

            $fileName = 'ketentuan_pelaporan/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $fileName);
            
            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                ['url' => asset('storage/' . $fileName)], 
                'Gambar berhasil diunggah'
            );
        } catch (ValidationException $e) {
            // Menggunakan method jsonValidationError dari BaseControllerFunction
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e);
        }
    }

    // Method untuk menghapus gambar
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
                
                // Menggunakan method jsonSuccess dari BaseControllerFunction
                return $this->jsonSuccess(
                    null, 
                    'Gambar berhasil dihapus'
                );
            } else {
                // Menggunakan method jsonError dari BaseControllerFunction
                return $this->jsonError(
                    new \Exception('Path gambar tidak valid'), 
                    '', 
                    400
                );
            }
        } catch (ValidationException $e) {
            // Menggunakan method jsonValidationError dari BaseControllerFunction
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e);
        }
    }
}