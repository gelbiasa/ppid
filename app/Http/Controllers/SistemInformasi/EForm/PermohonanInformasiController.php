<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\Controller;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PermohonanInformasiController extends Controller
{
    public $breadcrumb = 'Permohonan Informasi';
    public $pagename = 'SistemInformasi/EForm/PermohonanInformasi';
  
    private function getUserFolder()
    {
        $levelKode = Auth::user()->level->level_kode;
        return ($levelKode === 'ADM' || $levelKode === 'RPN') ? $levelKode : abort(403);
    }

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

    public function formPermohonanInformasi()
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

    public function storePermohonanInformasi(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            // PermohonanInformasiModel::validasiData($request);
            $result = PermohonanInformasiModel::validasiData($request);
            $result = PermohonanInformasiModel::createData($request);
            
            if ($result['success']) {
                return redirect("/SistemInformasi/EForm/$folder/PermohonanInformasi")
                    ->with('success', $result['message']);
            }

            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
