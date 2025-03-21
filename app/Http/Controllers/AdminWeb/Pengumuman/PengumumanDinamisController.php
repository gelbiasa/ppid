<?php

namespace App\Http\Controllers\AdminWeb\Pengumuman;

use App\Http\Controllers\TraitsController;
use App\Models\Website\Publikasi\Pengumuman\PengumumanDinamisModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class PengumumanDinamisController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Pengumuman Dinamis';
    public $pagename = 'AdminWeb/PengumumanDinamis';

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Pengaturan Pengumuman Dinamis',
            'list' => ['Home', 'Website', 'Pengumuman Dinamis']
        ];

        $page = (object) [
            'title' => 'Daftar Pengumuman Dinamis'
        ];

        $activeMenu = 'PengumumanDinamis';

        return view("AdminWeb/PengumumanDinamis.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function getData()
    {
        $result = PengumumanDinamisModel::selectData();
        $data = [];
        
        foreach ($result as $key => $pengumuman) {
            $row = [];
            $row[] = $key + 1;
            $row[] = $pengumuman->pd_nama_submenu;
            
            $row[] = $this->generateActionButtons(
                'AdminWeb/PengumumanDinamis', 
                $pengumuman->pengumuman_dinamis_id
            );
            
            $data[] = $row;
        }
        
        return response()->json(['data' => $data]);
    }

    public function addData()
    {
        return view("AdminWeb/PengumumanDinamis.create");
    }

    public function createData(Request $request)
    {
        try {
            PengumumanDinamisModel::validasiData($request);
            $result = PengumumanDinamisModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman dinamis berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat pengumuman dinamis');
        }
    }

    public function editData($id)
    {
        $pengumumanDinamis = PengumumanDinamisModel::detailData($id);

        return view("AdminWeb/PengumumanDinamis.update", [
            'pengumumanDinamis' => $pengumumanDinamis
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            PengumumanDinamisModel::validasiData($request);
            $result = PengumumanDinamisModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman dinamis berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui pengumuman dinamis');
        }
    }

    public function detailData($id)
    {
        $pengumumanDinamis = PengumumanDinamisModel::detailData($id);
        
        return view("AdminWeb/PengumumanDinamis.detail", [
            'pengumumanDinamis' => $pengumumanDinamis,
            'title' => 'Detail Pengumuman Dinamis'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $pengumumanDinamis = PengumumanDinamisModel::detailData($id);
            
            return view("AdminWeb/PengumumanDinamis.delete", [
                'pengumumanDinamis' => $pengumumanDinamis
            ]);
        }
        
        try {
            $result = PengumumanDinamisModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pengumuman dinamis berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus pengumuman dinamis');
        }
    }
}