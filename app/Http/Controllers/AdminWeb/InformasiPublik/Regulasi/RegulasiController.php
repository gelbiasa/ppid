<?php
namespace App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi;

use App\Http\Controllers\TraitsController;
use App\Models\Website\InformasiPublik\Regulasi\KategoriRegulasiModel;
use App\Models\Website\InformasiPublik\Regulasi\RegulasiModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class RegulasiController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Pengaturan Detail Regulasi';
    public $pagename = 'AdminWeb/InformasiPublik/Regulasi';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Detail Regulasi',
            'list' => ['Home', 'Informasi Publik', 'Detail Regulasi']
        ];

        $page = (object) [
            'title' => 'Daftar Regulasi'
        ];

        $activeMenu = 'Regulasi';
        
        // Gunakan pagination dan pencarian
        $regulasi = RegulasiModel::selectData(10, $search);

        return view("AdminWeb/InformasiPublik/Regulasi.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'regulasi' => $regulasi,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $regulasi = RegulasiModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/InformasiPublik/Regulasi.data', compact('regulasi', 'search'))->render();
        }
        
        return redirect()->route('adminweb/informasipublik/detail-regulasi');
    }

    public function addData()
    {
        $kategoriRegulasi = KategoriRegulasiModel::where('isDeleted', 0)->get();
        
        return view("AdminWeb/InformasiPublik/Regulasi.create", [
            'kategoriRegulasi' => $kategoriRegulasi
        ]);
    }

    public function createData(Request $request)
    {
        try {
            RegulasiModel::validasiData($request);
            $result = RegulasiModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat regulasi');
        }
    }

    public function editData($id)
    {
        $regulasi = RegulasiModel::detailData($id);
        $kategoriRegulasi = KategoriRegulasiModel::where('isDeleted', 0)->get();

        return view("AdminWeb/InformasiPublik/Regulasi.update", [
            'regulasi' => $regulasi,
            'kategoriRegulasi' => $kategoriRegulasi
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            RegulasiModel::validasiData($request);
            $result = RegulasiModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui regulasi');
        }
    }

    public function detailData($id)
    {
        $regulasi = RegulasiModel::detailData($id);
        
        return view("AdminWeb/InformasiPublik/Regulasi.detail", [
            'regulasi' => $regulasi,
            'title' => 'Detail Regulasi'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $regulasi = RegulasiModel::detailData($id);
            
            return view("AdminWeb/InformasiPublik/Regulasi.delete", [
                'regulasi' => $regulasi
            ]);
        }
        
        try {
            $result = RegulasiModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Regulasi berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus regulasi');
        }
    }
}