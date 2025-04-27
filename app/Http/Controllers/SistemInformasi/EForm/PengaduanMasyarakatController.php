<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PengaduanMasyarakatController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaduan Masyarakat';
    public $pagename = 'SistemInformasi/EForm/PengaduanMasyarakat';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'PengaduanMasyarakat';

        return view("SistemInformasi/EForm/$folder/PengaduanMasyarakat.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PengaduanMasyarakatModel::getTimeline();
        $ketentuanPelaporan = PengaduanMasyarakatModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'PengaduanMasyarakat';

        return view("SistemInformasi/EForm/$folder/PengaduanMasyarakat.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PengaduanMasyarakatModel::validasiData($request);
            $result = PengaduanMasyarakatModel::createData($request);

            if ($result['success']) {
                return $this->redirectSuccess("/SistemInformasi/EForm/$folder/PengaduanMasyarakat", $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan pengaduan');
        }
    }

    private function getUserFolder()
    {
        $hakAksesKode = Auth::user()->level->hak_akses_kode;
        return ($hakAksesKode === 'ADM' || $hakAksesKode === 'RPN') ? $hakAksesKode : abort(403);
    }
}