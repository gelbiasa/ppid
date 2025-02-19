<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\Controller;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PermohonanInformasiController extends Controller
{
    public function indexRPN()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi'; // Set the active menu

        return view('SistemInformasi/EForm/RPN/PermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function formPermohonanInformasiRPN()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view('SistemInformasi/EForm/RPN/PermohonanInformasi.pengisianForm', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function storePermohonanInformasiRPN(Request $request)
    {
        try {
            // Jalankan validasi dari model
            PermohonanInformasiModel::validasiData($request);
            
            // Lanjutkan dengan pembuatan permohonan
            $result = PermohonanInformasiModel::createData($request);

            if ($result['success']) {
                return redirect('/SistemInformasi/EForm/RPN/PermohonanInformasi')
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

    public function indexADM()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi'; // Set the active menu

        return view('SistemInformasi/EForm/ADM/PermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function formPermohonanInformasiADM()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view('SistemInformasi/EForm/ADM/PermohonanInformasi.pengisianForm', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function storePermohonanInformasiADM(Request $request)
    {
        try {
            // Jalankan validasi dari model
            PermohonanInformasiModel::validasiData($request);
            
            // Lanjutkan dengan pembuatan permohonan
            $result = PermohonanInformasiModel::createData($request);

            if ($result['success']) {
                return redirect('/SistemInformasi/EForm/ADM/PermohonanInformasi')
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
