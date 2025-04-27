<?php

namespace App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Website\InformasiPublik\LHKPN\LhkpnModel;

class LhkpnController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'LHKPN Tahun';
    public $pagename = 'AdminWeb/InformasiPublik/LhkpnTahun';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan LHKPN Tahun',
            'list' => ['Home', 'Pengaturan Lhkpn Tahun']
        ];

        $page = (object) [
            'title' => 'Daftar Lhkpn Tahun'
        ];

        $activeMenu = 'Lhkpn Tahun';
        
        // Gunakan pagination dan pencarian
        $lhkpn = LhkpnModel::selectData(10, $search);

        return view('AdminWeb/InformasiPublik/LhkpnTahun.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'lhkpn' => $lhkpn,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $lhkpn = LhkpnModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/InformasiPublik/LhkpnTahun.data', compact('lhkpn', 'search'))->render();
        }
        
        return redirect()->route('lhkpn-tahun.index');
    }

    public function addData()
    {
        return view('AdminWeb/InformasiPublik/LhkpnTahun.create');
    }

    public function createData(Request $request)
    {
        try {
            LhkpnModel::validasiData($request);
            $result = LhkpnModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data LHKPN berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat data LHKPN');
        }
    }

    public function editData($id)
    {
        $lhkpn = LhkpnModel::detailData($id);

        return view('AdminWeb/InformasiPublik/LhkpnTahun.update', [
            'lhkpn' => $lhkpn
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            LhkpnModel::validasiData($request);
            $result = LhkpnModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data Tahun Lhkpn berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui data LHKPN');
        }
    }

    public function detailData($id)
    {
        $lhkpn = LhkpnModel::detailData($id);
        
        return view('AdminWeb/InformasiPublik/LhkpnTahun.detail', [
            'lhkpn' => $lhkpn,
            'title' => 'Detail Tahun Lhkpn'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $lhkpn = LhkpnModel::detailData($id);
            
            return view('AdminWeb/InformasiPublik/LhkpnTahun.delete', [
                'lhkpn' => $lhkpn
            ]);
        }
        
        try {
            $result = LhkpnModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Data LHKPN berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus data LHKPN');
        }
    }
    
    /**
     * Upload gambar dari Summernote
     */
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

            $fileName = 'kategori_tahun_lhkpn/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
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