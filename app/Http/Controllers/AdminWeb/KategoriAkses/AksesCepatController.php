<?php

namespace App\Http\Controllers\AdminWeb\KategoriAkses;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\LandingPage\KategoriAkses\AksesCepatModel;
use App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;

class AksesCepatController extends Controller
{
     use TraitsController;

     public function index(Request $request)
     {
          $search = $request->query('search', '');
          $kategoriAksesId = $request->query('kategori_akses', null);

          $breadcrumb = (object) [
               'title' => 'Manajemen Akses Cepat',
               'list' => ['Home', 'Akses Cepat', 'Daftar']
          ];

          $page = (object) [
               'title' => 'Daftar Akses Cepat'
          ];

          $activeMenu = 'akses-cepat';

          // Ambil data kategori akses untuk filter
          $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->get();

          // Ambil data akses cepat
          $aksesCepat = AksesCepatModel::selectData(10, $search, $kategoriAksesId);

          return view('AdminWeb.AksesCepat.index', [
               'breadcrumb' => $breadcrumb,
               'page' => $page,
               'activeMenu' => $activeMenu,
               'aksesCepat' => $aksesCepat,
               'kategoriAkses' => $kategoriAkses,
               'search' => $search,
          ]);
     }

     public function getData(Request $request)
     {
          $search = $request->query('search', '');
          $aksesCepat = AksesCepatModel::selectData(10, $search);
     
          if ($request->ajax()) {
               return view('AdminWeb.AksesCepat.data', compact('aksesCepat', 'search'))->render();
          }
     
          return redirect()->route('akses-cepat.index');
     }

     public function addData()
     {
         $kategoriAkses = KategoriAksesModel::where('mka_judul_kategori', 'Akses Menu Cepat')->first();
         
         return view('AdminWeb.AksesCepat.create', compact('kategoriAkses'));
     }

     public function createData(Request $request)
     {
          try {
               AksesCepatModel::validasiData($request);
               $result = AksesCepatModel::createData($request);
               return response()->json($result);
          } catch (ValidationException $e) {
               return response()->json(AksesCepatModel::responValidatorError($e));
          } catch (\Exception $e) {
               return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat membuat Akses Cepat'));
          }
     }


     public function editData($id)
     {
          try {
               $aksesCepat = AksesCepatModel::findOrFail($id);
               $kategoriAkses = KategoriAksesModel::where('isDeleted', 0)->get();
     
               return view('AdminWeb.AksesCepat.update', [
                    'aksesCepat' => $aksesCepat,
                    'kategoriAkses' => $kategoriAkses
               ]);
          } catch (\Exception $e) {
               return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
          }
     }
     // Proses update akses cepat
     public function updateData(Request $request, $id)
     {
          try {
               AksesCepatModel::validasiData($request, $id);
               $result = AksesCepatModel::updateData($request, $id);
               return response()->json($result);
          } catch (ValidationException $e) {
               return response()->json(AksesCepatModel::responValidatorError($e));
          } catch (\Exception $e) {
               return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui Akses Cepat'));
          }
     }

     // Lihat detail akses cepat
     public function detailData($id)
     {
          try {
               $aksesCepat = AksesCepatModel::with('kategoriAkses')->findOrFail($id);

               return view('AdminWeb.AksesCepat.detail', [
                    'aksesCepat' => $aksesCepat,
                    'title' => 'Detail Akses Cepat'
               ]);
          } catch (\Exception $e) {
               return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
          }
     }
     public function deleteData(Request $request, $id)
     {
          if ($request->isMethod('get')) {
               try {
                    $aksesCepat = AksesCepatModel::with('kategoriAkses')->findOrFail($id);

                    return view('AdminWeb.AksesCepat.delete', [
                         'aksesCepat' => $aksesCepat
                    ]);
               } catch (\Exception $e) {
                    return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
               }
          }

          try {
               $result = AksesCepatModel::deleteData($id);
               return response()->json($result);
          } catch (\Exception $e) {
               return response()->json(AksesCepatModel::responFormatError($e, 'Terjadi kesalahan saat menghapus Akses Cepat'));
          }
     }
}