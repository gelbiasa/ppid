<?php

namespace App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
            'list' => ['Home', 'Pengaturan Detail LHKPN']
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

            DetailLhkpnModel::validasiData($request);
            $result = DetailLhkpnModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Detail LHKPN berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat Detail LHKPN');
        }
    }

    public function editData($id)
    {
        $detailLhkpn = DetailLhkpnModel::detailData($id);
        $tahunList = LhkpnModel::where('isDeleted', 0)->get();

        return view('AdminWeb/InformasiPublik/DetailLhkpn.update', [
            'detailLhkpn' => $detailLhkpn,
            'tahunList' => $tahunList
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            DetailLhkpnModel::validasiData($request, $id);
            $result = DetailLhkpnModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Detail LHKPN berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Detail LHKPN');
        }
    }
    
    public function detailData($id)
    {
        $detailLhkpn = DetailLhkpnModel::detailData($id);

        return view('AdminWeb/InformasiPublik/DetailLhkpn.detail', [
            'detailLhkpn' => $detailLhkpn,
            'title' => 'Detail LHKPN'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $detailLhkpn = DetailLhkpnModel::detailData($id);

            return view("AdminWeb/InformasiPublik/DetailLhkpn.delete", [
                'detailLhkpn' => $detailLhkpn
            ]);
        }

        try {
            $result = DetailLhkpnModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Detail LHKPN berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Detail LHKPN');
        }
    }
}