<?php

namespace App\Http\Controllers\AdminWeb\KategoriAkses;

use App\Http\Controllers\TraitsController;
use App\Models\Website\LandingPage\KategoriAkses\AksesCepatModel;
use App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class AksesCepatController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Akses Cepat';
    public $pagename = 'AdminWeb/AksesCepat';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriAksesId = $request->query('kategori_akses', null);

        $breadcrumb = (object) [
            'title' => 'Pengaturan Akses Cepat',
            'list' => ['Home', 'Pengaturan Akses Cepat']
        ];

        $page = (object) [
            'title' => 'Daftar Akses Cepat'
        ];

        $activeMenu = 'akses-cepat';

        // Ambil data kategori akses untuk filter
        $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->get();

        // Ambil data akses cepat
        $aksesCepat = AksesCepatModel::selectData(10, $search, $kategoriAksesId);

        return view('AdminWeb/AksesCepat.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'aksesCepat' => $aksesCepat,
            'kategoriAkses' => $kategoriAkses,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $aksesCepat = AksesCepatModel::selectData(10, $search);

        if ($request->ajax()) {
            return view('AdminWeb/AksesCepat.data', compact('aksesCepat', 'search'))->render();
        }

        return redirect()->route('akses-cepat.index');
    }

    public function addData()
    {
        $kategoriAkses = KategoriAksesModel::where('mka_judul_kategori', 'Akses Menu Cepat')
            ->where('kategori_akses_id', '=', 2)
            ->where('isDeleted', 0)
            ->first();
        if (!$kategoriAkses) {
            // untuk mendapatkan Kategori Akses yang tersedia jika yang spesifik tidak ditemukan
            $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->first();
        }

        return view('AdminWeb/AksesCepat.create', compact('kategoriAkses'));
    }

    public function createData(Request $request)
    {
        try {
            AksesCepatModel::validasiData($request);
            $result = AksesCepatModel::createData($request);
            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Akses Cepat berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat Akses Cepat');
        }
    }

    public function editData($id)
    {
            $aksesCepat = AksesCepatModel::detailData($id);
            $kategoriAkses = KategoriAksesModel::where('mka_judul_kategori', 'Akses Menu Cepat')->first();

            return view('AdminWeb/AksesCepat.update', [
                'aksesCepat' => $aksesCepat,
                'kategoriAkses' => $kategoriAkses
            ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            AksesCepatModel::validasiData($request);
            $result = AksesCepatModel::updateData($request, $id);
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Akses Cepat berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Akses Cepat');
        }
    }

    public function detailData($id)
    {
            $aksesCepat = AksesCepatModel::detailData($id);

            return view('AdminWeb/AksesCepat.detail', [
                'aksesCepat' => $aksesCepat,
                'title' => 'Detail Akses Cepat'
            ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
                $aksesCepat = AksesCepatModel::detailData($id);

                return view('AdminWeb/AksesCepat.delete', [
                    'aksesCepat' => $aksesCepat
                ]);
        }

        try {
            $result = AksesCepatModel::deleteData($id);
            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Akses Cepat berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus Akses Cepat');
        }
    }
}