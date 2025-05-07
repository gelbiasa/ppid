<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PernyataanKeberatanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pernyataan Keberatan';
    public $pagename = 'SistemInformasi/EForm/PernyataanKeberatan';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pernyataan Keberatan',
            'list' => ['Home', 'Pernyataan Keberatan']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pernyataan Keberatan'
        ];

        $activeMenu = 'PernyataanKeberatan';

        return view("SistemInformasi/EForm/$folder/PernyataanKeberatan.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PernyataanKeberatanModel::getTimeline();
        $ketentuanPelaporan = PernyataanKeberatanModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pernyataan Keberatan',
            'list' => ['Home', 'Pernyataan Keberatan', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pernyataan Keberatan'
        ];

        $activeMenu = 'PernyataanKeberatan';

        return view("SistemInformasi/EForm/$folder/PernyataanKeberatan.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PernyataanKeberatanModel::validasiData($request);
            $result = PernyataanKeberatanModel::createData($request);

            if ($result['success']) {
                // Tentukan URL redirect berdasarkan folder
                if ($folder === 'RPN') {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan');
                } else {
                    $redirectUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin');
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
