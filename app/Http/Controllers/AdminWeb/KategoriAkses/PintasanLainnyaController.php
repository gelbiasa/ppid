<?php

namespace App\Http\Controllers\AdminWeb\KategoriAkses;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;
use App\Models\Website\LandingPage\KategoriAkses\PintasanLainnyaModel;

class PintasanLainnyaController extends Controller
{
    use TraitsController;
     
    public $breadcrumb = 'Pengaturan Pintasan Lainnya';
    public $pagename = 'AdminWeb/PintasanLainnya';

    public function index(Request $request) 
    {
        $search = $request->query('search', '');
     
        $breadcrumb = (object) [
            'title' => 'Pengaturan Pintasan Lainnya',
            'list' => ['Home', 'Pengaturan Pintasan Lainnya']
        ];
        $page = (object) [
            'title' => 'Daftar Pintasan Lainnya'
        ];

        $activeMenu = 'pintasan-lainnya';
         // Ambil data kategori akses untuk filter
         $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->get();

        $pintasanLainnya = PintasanLainnyaModel::selectData(10, $search);

        return view("AdminWeb/PintasanLainnya.index",[
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'pintasanLainnya' => $pintasanLainnya,
            'kategoriAkses' => $kategoriAkses,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $pintasanLainnya = PintasanLainnyaModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/PintasanLainnya.data', compact('pintasanLainnya', 'search'))->render();
        }
        
        return redirect()->route('pintasan-lainnya.index');
    }

    public function addData()
    {
        // Perbaikan: menggunakan kategori_akses_id bukan id dan quoting string dengan benar
        $kategoriAkses = KategoriAksesModel::where('mka_judul_kategori', '=', 'Pintasan Lainnya')
            ->where('kategori_akses_id', '=', 2)
            ->where('isDeleted', 0)
            ->first();
    
        if (!$kategoriAkses) {
            // Fallback untuk mendapatkan Kategori Akses yang tersedia jika yang spesifik tidak ditemukan
            $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->first();
        }
    
        return view('AdminWeb/PintasanLainnya.create', compact('kategoriAkses'));
    }

    public function createData(Request $request)
    {
        try {
            PintasanLainnyaModel::validasiData($request);
            $result = PintasanLainnyaModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pintasan lainnya berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat pintasan lainnya');
        }
    }

    public function editData($id)
    {
        $pintasanLainnya = PintasanLainnyaModel::detailData($id);

        return view("AdminWeb/PintasanLainnya.update", [
            'pintasanLainnya' => $pintasanLainnya
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            PintasanLainnyaModel::validasiData($request);
            $result = PintasanLainnyaModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pintasan lainnya berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui pintasan lainnya');
        }
    }

    public function detailData($id)
    {
        $pintasanLainnya = PintasanLainnyaModel::detailData($id);
        
        return view("AdminWeb/PintasanLainnya.detail", [
            'pintasanLainnya' => $pintasanLainnya,
            'title' => 'Detail Pintasan Lainnya'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $pintasanLainnya = PintasanLainnyaModel::detailData($id);
            
            return view("AdminWeb/PintasanLainnya.delete", [
                'pintasanLainnya' => $pintasanLainnya
            ]);
        }
        
        try {
            $result = PintasanLainnyaModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Pintasan lainnya berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus pintasan lainnya');
        }
    }
}