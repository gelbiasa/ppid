<?php

namespace App\Http\Controllers\AdminWeb\KategoriAkses;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\LandingPage\KategoriAkses\PintasanLainnyaModel;
use App\Models\Website\LandingPage\KategoriAkses\DetailPintasanLainnyaModel;

class DetailPintasanLainnyaController extends Controller
{
     use TraitsController;
     
     public $breadcrumb = 'Pengaturan Detail Pintasan Lainnya';
     public $pagename = 'AdminWeb/DetailPintasanLainnya';
     
     public function index(Request $request)
     {
          $search = $request->query('seacrh', '');
          
          $breadcrumb = (object) [
           'title' => 'Pengaturan Detail Pintasan Lainnya',
           'list' => ['Home', 'Pengaturan Detail Pintasan Lainnya']
               
          ];
          $page = (object) [
           'title' => 'Daftar Detail Pintasan Lainnta'
          ];
          $activeMenu = 'DetailPintasanLainnya';
          
          $pintasanLainnya = PintasanLainnyaModel::where('isDeleted', 0)->get();
          
          $detailPintasanLainnya = DetailPintasanLainnyaModel :: selectData(10, $search);

          return view ("AdminWeb/DetailPintasanLainnya.index",[
               'breadcrumb' => $breadcrumb,
               'page' => $page,
               'activeMenu' => $activeMenu,
               'pintasanLainnya' => $pintasanLainnya,
               'detailPintasanLainnya' => $detailPintasanLainnya,
               'search' => $search  
          ]);   
     }
     public function getData(Request $request)
     {
      $search = $request-> query('seacrh', '');
      $detailPintasanLainnya = DetailPintasanLainnyaModel::selectData(10, $search);
      if ($request-> ajax()) {
          return view('AdminWeb/DetailPintasanLainnya.data', compact('detailPintasanLainnya', 'search'))->render();
      }
      return redirect()->route('DetailPintasanLainnya.index');
          
     }
     public function addData()
     {
          $pintasanLainnya = PintasanLainnyaModel::where('isDeleted', 0)->get();
        return view("AdminWeb/DetailPintasanLainnya.create", compact('pintasanLainnya'));
     }
     public function createData(Request $request)
     {
         try {
             DetailPintasanLainnyaModel::validasiData($request);
             $result = DetailPintasanLainnyaModel::createData($request);
 
             return $this->jsonSuccess(
                 $result['data'] ?? null, 
                 $result['message'] ?? 'detail pintasan lainnya berhasil dibuat'
             );
         } catch (ValidationException $e) {
             return $this->jsonValidationError($e);
         } catch (\Exception $e) {
             return $this->jsonError($e, 'Terjadi kesalahan saat membuat detail pintasan Lainnya');
         }
     }
     public function editData($id)
     {
         $detailPintasanLainnya = DetailPintasanLainnyaModel::detailData($id);
         $pintasanLainnya = PintasanLainnyaModel::where('isDeleted', 0)->get();
 
         return view("AdminWeb/DetailPintasanLainnya.update", [
             'detailPintasanLainnya' => $detailPintasanLainnya,
             'pintasanLainnya' => $pintasanLainnya
         ]);
     }
     public function updateData(Request $request, $id)
    {
        try {
            DetailPintasanLainnyaModel::validasiData($request, $id);
            $result = DetailPintasanLainnyaModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail pintasan lainnya berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui detail pintasan lainnya');
        }
    }
    public function detailData($id)
    {
        $detailPintasanLainnya = DetailPintasanLainnyaModel::detailData($id);
        
        return view("AdminWeb/DetailPintasanLainnya.detail", [
            'detailPintasanLainnya' => $detailPintasanLainnya,
            'title' => 'Detail Pintasan Lainnya'
        ]);
    }
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $detailPintasanLainnya = DetailPintasanLainnyaModel::detailData($id);
            
            return view("AdminWeb/DetailPintasanLainnya.delete", [
                'detailPintasanLainnya' => $detailPintasanLainnya
            ]);
        }
        
        try {
            $result = DetailPintasanLainnyaModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Detail pintasan lainnya berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus detail pintasan lainnya');
        }
    }

}