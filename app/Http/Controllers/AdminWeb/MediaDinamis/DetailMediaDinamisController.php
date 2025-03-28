<?php

namespace App\Http\Controllers\AdminWeb\MediaDinamis;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\LandingPage\MediaDinamis\MediaDinamisModel;
use App\Models\Website\LandingPage\MediaDinamis\DetailMediaDinamisModel;

class DetailMediaDinamisController extends Controller
{
    use TraitsController;
 
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen Detail Media Dinamis',
            'list' => ['Home', 'Media Dinamis', 'Detail Media']
        ];

        $page = (object) [
            'title' => 'Daftar Detail Media Dinamis'
        ];
        
        $activeMenu = 'media-detail';
        
        // Get kategori for filter
        $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
        
        // Get data with filters
        $detailMediaDinamis = DetailMediaDinamisModel::selectData(10, $search, $kategori);

        return view('AdminWeb.DetailMediaDinamis.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'detailMediaDinamis' => $detailMediaDinamis,
            'search' => $search,
            'kategoris' => $kategoris,
            'selectedKategori' => $kategori
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');
        $detailMediaDinamis = DetailMediaDinamisModel::selectData(10, $search, $kategori);
        
        if ($request->ajax()) {
            return view('AdminWeb.DetailMediaDinamis.data', compact('detailMediaDinamis', 'search', 'kategori'))->render();
        }
        
        return redirect()->route('media-detail.index');
    }
  
    public function addData()
    {
        $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
        return view('AdminWeb.DetailMediaDinamis.create', compact('kategoris'));
    }

    public function createData(Request $request)
    {
        try {
            // Proses upload file jika ada
            $this->processFileUpload($request);
            
            // Validasi dan buat data
            DetailMediaDinamisModel::validasiData($request);
            $result = DetailMediaDinamisModel::createData($request);
            
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(DetailMediaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(DetailMediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat membuat detail media dinamis'));
        }
    }

    public function editData($id)
    {
        try {
            $detailMediaDinamis = DetailMediaDinamisModel::findOrFail($id);
            $kategoris = MediaDinamisModel::where('isDeleted', 0)->get();
            
            return view('AdminWeb.DetailMediaDinamis.update', [
                'detailMediaDinamis' => $detailMediaDinamis,
                'kategoris' => $kategoris
            ]);
        } catch (\Exception $e) {
            return response()->json(DetailMediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            // Ambil data detail media dinamis yang akan diupdate
            $detailMediaDinamis = DetailMediaDinamisModel::findOrFail($id);

            // Cek apakah ada file upload
            $hasNewFile = $request->hasFile('media_file');
            
            // Proses validasi dengan mengirim ID
            DetailMediaDinamisModel::validasiData($request, $id);

            // Jika ada file upload baru, proses file
            if ($hasNewFile) {
                $this->processFileUpload($request, $id);
            } else {
                // Jika tidak ada file baru, gunakan media_upload yang sudah ada
                $request->merge([
                    't_detail_media_dinamis' => array_merge(
                        $request->t_detail_media_dinamis,
                        ['dm_media_upload' => $detailMediaDinamis->dm_media_upload]
                    )
                ]);
            }

            // Proses update data
            $result = DetailMediaDinamisModel::updateData($request, $id);
            
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(DetailMediaDinamisModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(DetailMediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui detail media dinamis'));
        }
    }


    public function detailData($id)
    {
            // Change this to match your model's method name for relationships
            $detailMedia = DetailMediaDinamisModel::with('mediaDinamis')
                ->findOrFail($id);
            
            return view('AdminWeb.DetailMediaDinamis.detail', [
                'detailMedia' => $detailMedia,
                'title' => 'Detail Media Dinamis'
            ]);
    }

   public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $detailMediaDinamis = DetailMediaDinamisModel::with('mediaDinamis')->findOrFail($id);
                
                return view('AdminWeb.DetailMediaDinamis.delete', [
                    'detailMediaDinamis' => $detailMediaDinamis
                ]);
            } catch (\Exception $e) {
                return response()->json(DetailMediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $detailMediaDinamis = DetailMediaDinamisModel::findOrFail($id);
            
            // Hapus file dari storage jika ada
            if ($detailMediaDinamis->dm_type_media == 'file' && $detailMediaDinamis->dm_media_upload) {
                $filePath = str_replace('storage/', '', $detailMediaDinamis->dm_media_upload);
                Storage::disk('public')->delete($filePath);
            }
            
            $result = DetailMediaDinamisModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(DetailMediaDinamisModel::responFormatError($e, 'Terjadi kesalahan saat menghapus detail media dinamis'));
        }
    }

    private function processFileUpload(Request $request, $id = null)
    {
        if (!$this->isFileUploadRequest($request)) {
            return;
        }
        
        $file = $request->file('media_file');
        $fileExt = $file->getClientOriginalExtension();
        
        // Generate random filename
        $randomName = Str::random(20) . '.' . $fileExt;
        
        // Hapus file lama jika sedang update
        if ($id) {
            $detailMediaDinamis = DetailMediaDinamisModel::findOrFail($id);
            if ($detailMediaDinamis->dm_type_media == 'file' && $detailMediaDinamis->dm_media_upload) {
                $oldFilePath = $detailMediaDinamis->dm_media_upload;
                Storage::disk('public')->delete($oldFilePath);
            }
        }
        
        // Simpan file di storage/app/public/media_dinamis
        $path = $file->storeAs('media_dinamis', $randomName, 'public');
        
        // Update request
        $request->merge([
            't_detail_media_dinamis' => array_merge(
                $request->t_detail_media_dinamis,
                ['dm_media_upload' => $path]
            )
        ]);
    }
    
    private function isFileUploadRequest(Request $request)
    {
        return isset($request->t_detail_media_dinamis['dm_type_media']) &&
               $request->t_detail_media_dinamis['dm_type_media'] == 'file' &&
               $request->hasFile('media_file');
    }
}