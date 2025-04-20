<?php
namespace App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\InformasiPublik\Regulasi\RegulasiDinamisModel;
use Illuminate\Support\Facades\Log;

class RegulasiDinamisController extends Controller
{
    use TraitsController;
    
    public $breadcrumb = 'Pengaturan Regulasi Dinamis';
    public $pagename = 'AdminWeb/InformasiPublik/RegulasiDinamis';
    
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Regulasi Dinamis',
            'list' => ['Home', 'Website', 'Regulasi Dinamis']
        ];

        $page = (object) [
            'title' => 'Daftar Regulasi Dinamis'
        ];

        $activeMenu = 'regulasi-dinamis';

        // Gunakan pagination dan pencarian
        $RegulasiDinamis = RegulasiDinamisModel::selectData(10, $search);

        return view("AdminWeb/InformasiPublik/RegulasiDinamis.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'RegulasiDinamis' => $RegulasiDinamis,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $RegulasiDinamis = RegulasiDinamisModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb.InformasiPublik.RegulasiDinamis.data', compact('RegulasiDinamis', 'search'))->render();
        }
        
        return redirect()->route('regulasi-dinamis.index');
    }

    public function addData()
    {
        try {
            return view("AdminWeb.InformasiPublik.RegulasiDinamis.create");
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in addData: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat form tambah data'], 500);
        }
    }

    public function createData(Request $request)
    {
        try {
            RegulasiDinamisModel::validasiData($request);
            $result = RegulasiDinamisModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in createData: ' . $e->getMessage());
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat regulasi dinamis');
        }
    }

    public function editData($id)
    {
        try {
            $RegulasiDinamis = RegulasiDinamisModel::detailData($id);

            return view("AdminWeb.InformasiPublik.RegulasiDinamis.update", [
                'RegulasiDinamis' => $RegulasiDinamis
            ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in editData: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat form edit data'], 500);
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            RegulasiDinamisModel::validasiData($request);
            $result = RegulasiDinamisModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in updateData: ' . $e->getMessage());
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui regulasi dinamis');
        }
    }

    public function detailData($id)
    {
        try {
            $RegulasiDinamis = RegulasiDinamisModel::detailData($id);
        
            return view("AdminWeb.InformasiPublik.RegulasiDinamis.detail", [
                'RegulasiDinamis' => $RegulasiDinamis,
                'title' => 'Detail Regulasi Dinamis'
            ]);
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in detailData: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat detail data'], 500);
        }
    }

    public function deleteData(Request $request, $id)
    {
        try {
            if ($request->isMethod('get')) {
                $RegulasiDinamis = RegulasiDinamisModel::detailData($id);
                
                return view("AdminWeb.InformasiPublik.RegulasiDinamis.delete", [
                    'RegulasiDinamis' => $RegulasiDinamis
                ]);
            }
            
            $result = RegulasiDinamisModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi dinamis berhasil dihapus'
            );
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in deleteData: ' . $e->getMessage());
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus regulasi dinamis');
        }
    }
}