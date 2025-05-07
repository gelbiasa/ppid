<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermohonanPerawatanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Permohonan Pemeliharaan Sarana Prasarana';
    public $pagename = 'SistemInformasi/EForm/PermohonanPerawatan';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana',
            'list' => ['Home', 'Permohonan Pemeliharaan Sarana Prasarana']
        ];

        $page = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana'
        ];

        $activeMenu = 'PermohonanPerawatan';

        return view("SistemInformasi/EForm/$folder/PermohonanPerawatan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PermohonanPerawatanModel::getTimeline();
        $ketentuanPelaporan = PermohonanPerawatanModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Pemeliharaan Sarana Prasarana',
            'list' => ['Home', 'Permohonan Pemeliharaan Sarana Prasarana', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Pemeliharaan Sarana Prasarana'
        ];

        $activeMenu = 'PermohonanPerawatan';

        return view("SistemInformasi/EForm/$folder/PermohonanPerawatan.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PermohonanPerawatanModel::validasiData($request);
            $result = PermohonanPerawatanModel::createData($request);

            if ($result['success']) {
                // Tentukan URL redirect berdasarkan folder
                if ($folder === 'RPN') {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana');
                } else {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana-admin');
                }
                
                return $this->redirectSuccess($redirectUrl, $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan Permohonan Pemeliharaan Sarana Prasarana');
        }
    }

    private function getUserFolder()
    {
        $hakAksesKode = Auth::user()->level->hak_akses_kode;
        
        // Jika user adalah RPN, gunakan folder RPN
        // Jika tidak (ADM, ADT, atau lainnya), gunakan folder ADM
        return ($hakAksesKode === 'RPN') ? 'RPN' : 'ADM';
    }
}
