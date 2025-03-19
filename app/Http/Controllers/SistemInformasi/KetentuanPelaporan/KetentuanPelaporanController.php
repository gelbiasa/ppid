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
            
            $actions = '
                <button class="btn btn-sm btn-info" onclick="modalAction(\'' . url("SistemInformasi/KetentuanPelaporan/editData/{$ketentuanPelaporan->ketentuan_pelaporan_id}") . '\')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-primary" onclick="modalAction(\'' . url("SistemInformasi/KetentuanPelaporan/detailData/{$ketentuanPelaporan->ketentuan_pelaporan_id}") . '\')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction(\'' . url("SistemInformasi/KetentuanPelaporan/deleteData/{$ketentuanPelaporan->ketentuan_pelaporan_id}") . '\')">
                    <i class="fas fa-trash"></i> Hapus
                </button>';
            
            $row[] = $actions;
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

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KetentuanPelaporanModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KetentuanPelaporanModel::responFormatError($e, 'Terjadi kesalahan saat membuat ketentuan pelaporan'));
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

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(KetentuanPelaporanModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(KetentuanPelaporanModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui ketentuan pelaporan'));
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
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(KetentuanPelaporanModel::responFormatError($e, 'Terjadi kesalahan saat menghapus ketentuan pelaporan'));
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
            
            // Menggunakan method uploadFile dari TraitsModel, tetapi sekarang dipanggil langsung di controller
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file yang diunggah'
                ], 400);
            }

            $fileName = 'ketentuan_pelaporan/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public', $fileName);
            
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $fileName)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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
                
                return response()->json([
                    'success' => true,
                    'message' => 'Gambar berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Path gambar tidak valid'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}