<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermohonanInformasiController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Permohonan Informasi';
    public $pagename = 'SistemInformasi/EForm/PermohonanInformasi';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view("SistemInformasi/EForm/$folder/PermohonanInformasi.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PermohonanInformasiModel::getTimeline();
        $ketentuanPelaporan = PermohonanInformasiModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view("SistemInformasi/EForm/$folder/PermohonanInformasi.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            // Dapatkan folder untuk menentukan redirect
            $folder = $this->getUserFolder();
            
            PermohonanInformasiModel::validasiData($request);
            $result = PermohonanInformasiModel::createData($request);

            if ($result['success']) {
                // Tentukan URL redirect berdasarkan folder
                if ($folder === 'RPN') {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi');
                } else {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi-admin');
                }
                
                return $this->redirectSuccess($redirectUrl, $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan permohonan');
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
