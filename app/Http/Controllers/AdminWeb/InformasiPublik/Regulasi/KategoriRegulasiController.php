<?php
namespace App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi;

use App\Http\Controllers\TraitsController;
use App\Models\Website\InformasiPublik\Regulasi\KategoriRegulasiModel;
use App\Models\Website\InformasiPublik\Regulasi\RegulasiDinamisModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class KategoriRegulasiController extends Controller
{
    use TraitsController;
     
    public $breadcrumb = 'Pengaturan Kategori Regulasi';
    public $pagename = 'AdminWeb/InformasiPublik/KategoriRegulasi';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Kategori Regulasi',
            'list' => ['Home', 'Pengaturan Kategori Regulasi']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Regulasi'
        ];

        $activeMenu = 'kategori-regulasi';
        
        $kategoriRegulasi = KategoriRegulasiModel::selectData(10, $search);
        $regulasiDinamis = RegulasiDinamisModel::where('isDeleted', 0)->get();

        return view("AdminWeb/InformasiPublik/KategoriRegulasi.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriRegulasi' => $kategoriRegulasi,
            'regulasiDinamis' => $regulasiDinamis,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriRegulasi = KategoriRegulasiModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/InformasiPublik/KategoriRegulasi.data', compact('kategoriRegulasi', 'search'))->render();
        }
        
        return redirect()->route('kategori-regulasi.index');
    }

    public function addData()
    {
        $regulasiDinamis = RegulasiDinamisModel::where('isDeleted', 0)->get();
        return view("AdminWeb/InformasiPublik/KategoriRegulasi.create", compact('regulasiDinamis'));
    }

    public function createData(Request $request)
    {
        try {
            KategoriRegulasiModel::validasiData($request);
            $result = KategoriRegulasiModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori regulasi berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat kategori regulasi');
        }
    }

    public function editData($id)
    {
        $kategoriRegulasi = KategoriRegulasiModel::detailData($id);
        $regulasiDinamis = RegulasiDinamisModel::all();

        return view("AdminWeb/InformasiPublik/KategoriRegulasi.update", [
            'kategoriRegulasi' => $kategoriRegulasi,
            'regulasiDinamis' => $regulasiDinamis
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            KategoriRegulasiModel::validasiData($request);
            $result = KategoriRegulasiModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori regulasi berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui kategori regulasi');
        }
    }

    public function detailData($id)
    {
        $kategoriRegulasi = KategoriRegulasiModel::detailData($id);
        
        return view("AdminWeb/InformasiPublik/KategoriRegulasi.detail", [
            'kategoriRegulasi' => $kategoriRegulasi,
            'title' => 'Detail Kategori Regulasi Dinamis'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $kategoriRegulasi = KategoriRegulasiModel::detailData($id);
            
            return view("AdminWeb/InformasiPublik/KategoriRegulasi.delete", [
                'kategoriRegulasi' => $kategoriRegulasi
            ]);
        }
        
        try {
            $result = KategoriRegulasiModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori regulasi berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus kategori regulasi');
        }
    }
}