<?php

namespace App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\InformasiPublik\LHKPN\LhkpnModel;
use App\Models\Website\InformasiPublik\LHKPN\DetailLhkpnModel;

class DetailLhkpnController extends Controller
{
    use TraitsController;
    
    public $breadcrumb = 'Detail Lhkpn';
    public $pagename = 'AdminWeb/InformasiPublik/DetailLhkpn';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
    
        $breadcrumb = (object) [
            'title' => 'Pengaturan Detail LHKPN',
            'list' => ['Home', 'Website', 'Detail LHKPN']
        ];
    
        $page = (object) [
            'title' => 'Daftar Detail LHKPN'
        ];
        
        $activeMenu = 'detail-lhkpn';
        
        // Get data with filters
        $detailLhkpn = DetailLhkpnModel::selectData(10, $search);
    
        return view('AdminWeb/InformasiPublik/DetailLhkpn.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'detailLhkpn' => $detailLhkpn,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', ''); 
    
        $detailLhkpn = DetailLhkpnModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/InformasiPublik/DetailLhkpn.data', compact('detailLhkpn', 'search'))->render();
        }
        
        return redirect()->route('detail-lhkpn.index');
    }
  
    public function addData()
    {
        $tahunList = LhkpnModel::where('isDeleted', 0)->get();
        return view('AdminWeb/InformasiPublik/DetailLhkpn.create', compact('tahunList'));
    }

    public function createData(Request $request)
    {
        try {
            // Proses upload file jika ada
            $this->processFileUpload($request);
            
            // Validasi dan buat data
            DetailLhkpnModel::validasiData($request);
            $result = DetailLhkpnModel::createData($request);
            
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(DetailLhkpnModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(DetailLhkpnModel::responFormatError($e, 'Terjadi kesalahan saat membuat detail LHKPN'));
        }
    }

    public function editData($id)
    {
        try {
            $detailLhkpn = DetailLhkpnModel::findOrFail($id);
            $tahunList = LhkpnModel::where('isDeleted', 0)->get();
            
            return view('AdminWeb/InformasiPublik/DetailLhkpn.update', [
                'detailLhkpn' => $detailLhkpn,
                'tahunList' => $tahunList
            ]);
        } catch (\Exception $e) {
            return response()->json(DetailLhkpnModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            // Ambil data detail LHKPN yang akan diupdate
            $detailLhkpn = DetailLhkpnModel::findOrFail($id);

            // Cek apakah ada file upload
            $hasNewFile = $request->hasFile('lhkpn_file');
            
            // Proses validasi dengan mengirim ID
            DetailLhkpnModel::validasiData($request, $id);

            // Jika ada file upload baru, proses file
            if ($hasNewFile) {
                $this->processFileUpload($request, $id);
            } else {
                // Jika tidak ada file baru, gunakan dl_file_lhkpn yang sudah ada
                $request->merge([
                    't_detail_lhkpn' => array_merge(
                        $request->t_detail_lhkpn,
                        ['dl_file_lhkpn' => $detailLhkpn->dl_file_lhkpn]
                    )
                ]);
            }

            // Proses update data
            $result = DetailLhkpnModel::updateData($request, $id);
            
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(DetailLhkpnModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(DetailLhkpnModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui detail LHKPN'));
        }
    }

    public function detailData($id)
    {
        $detailLhkpn = DetailLhkpnModel::with('lhkpn')
            ->findOrFail($id);
        
        return view('AdminWeb/InformasiPublik/DetailLhkpn.detail', [
            'detailLhkpn' => $detailLhkpn,
            'title' => 'Detail LHKPN'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $detailLhkpn = DetailLhkpnModel::with('lhkpn')->findOrFail($id);
                
                return view('AdminWeb/InformasiPublik/DetailLhkpn.delete', [
                    'detailLhkpn' => $detailLhkpn
                ]);
            } catch (\Exception $e) {
                return response()->json(DetailLhkpnModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $detailLhkpn = DetailLhkpnModel::findOrFail($id);
            
            // Hapus file dari storage jika ada
            if ($detailLhkpn->dl_file_lhkpn) {
                Storage::delete($detailLhkpn->dl_file_lhkpn);
            }
            
            $result = DetailLhkpnModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(DetailLhkpnModel::responFormatError($e, 'Terjadi kesalahan saat menghapus detail LHKPN'));
        }
    }

    private function processFileUpload(Request $request, $id = null)
    {
        if (!$request->hasFile('lhkpn_file')) {
            return;
        }
        
        $file = $request->file('lhkpn_file');
        $fileExt = $file->getClientOriginalExtension();
        
        // Generate nama file acak
        $randomName = Str::random(20) . '.' . $fileExt;
        
        // Hapus file lama jika sedang update
        if ($id) {
            $detailLhkpn = DetailLhkpnModel::findOrFail($id);
            if ($detailLhkpn->dl_file_lhkpn) {
                Storage::delete($detailLhkpn->dl_file_lhkpn);
            }
        }
        
        // Simpan file di storage/app/public/lhkpn
        $path = $file->storeAs('lhkpn', $randomName, 'public');
        
        // Update request dengan path yang benar
        $request->merge([
            't_detail_lhkpn' => array_merge(
                $request->t_detail_lhkpn,
                ['dl_file_lhkpn' => $path]
            )
        ]);
    }
}