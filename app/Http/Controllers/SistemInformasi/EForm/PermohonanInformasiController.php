<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\Controller;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PermohonanInformasiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi'; // Set the active menu

        return view('SistemInformasi/EForm/PermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function formPermohonanInformasi()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view('SistemInformasi/EForm/PermohonanInformasi.pengisianForm', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function storePermohonanInformasi(Request $request)
    {
        try {
            // Jalankan validasi dari model
            PermohonanInformasiModel::validasiData($request);
            
            // Lanjutkan dengan pembuatan permohonan
            $result = PermohonanInformasiModel::createData($request);

            if ($result['success']) {
                return redirect('/SistemInformasi/EForm/PermohonanInformasi')
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
