<?php
namespace App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use App\Models\Website\InformasiPublik\TabelDinamis\IpDinamisTabelModel;

class IpDinamisTabelController extends Controller 
{
     use TraitsController;

     public $breadcrumb = 'Ip Dinamis Tabel';
     public $pagename = 'AdminWeb/InformasiPublik/IpDinamisTabel';
     
     public function index (Request $request)
     {
          $search = $request->query('search', '');

          $breadcrumb = (object)[
               'title' => 'Pengaturan IpDinamis Tabel',
               'list' => ['Home', 'Pengaturan IpDinamis Tabel']
          ];

          $page = (object)[
               'title' => 'Daftar IpDinamis Tabel'
          ];
          
          $activeMenu = 'IpDinamisTabel';

          $ipDinamisTabel = IpDinamisTabelModel:: selectData(10, $search);
          
          
        return view("AdminWeb/InformasiPublik/IpDinamisTabel.index", [
          'breadcrumb' => $breadcrumb,
          'page' => $page,
          'activeMenu' => $activeMenu,
          'ipDinamisTabel' => $ipDinamisTabel,
          'search' => $search
      ]);
          
     }
     
     public function getData(Request $request)
     {
         $search = $request->query('search', '');
         $ipDinamisTabel = IpDinamisTabelModel::selectData(10, $search);
         
         if ($request->ajax()) {
             return view('AdminWeb/InformasiPublik/IpDinamisTabel.data', compact('ipDinamisTabel', 'search'))->render();
         }
         
         return redirect()->route('IpDinamisTabel.index');
     }
    
    public function addData()
    {
        return view("AdminWeb/InformasiPublik/IpDinamisTabel.create");
    }
    
    public function createData(Request $request)
    {
        try {
            IpDinamisTabelModel::validasiData($request);
            $result = IpDinamisTabelModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'IpDinamis Tabel berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat IpDinamis Tabel');
        }
    }
    public function editData($id)
    {
        $ipDinamisTabel= IpDinamisTabelModel::detailData($id);

        return view("AdminWeb/InformasiPublik/IpDinamisTabel.update", [
            'IpDinamisTabel' => $ipDinamisTabel
        ]);
    }
    public function updateData(Request $request, $id)
    {
        try {
            IpDinamisTabelModel::validasiData($request);
            $result = IpDinamisTabelModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'IpDinamis Tabel berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui IpDinamis Tabel');
        }
    }

    public function detailData($id)
    {
        $ipDinamisTabel = IpDinamisTabelModel::detailData($id);
        
        return view("AdminWeb/InformasiPublik/IpDinamisTabel.detail", [
            'IpDinamisTabel' => $ipDinamisTabel,
            'title' => 'Detail IpDinamis Tabel'
        ]);
     }

     public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ipDinamisTabel = IpDinamisTabelModel::detailData($id);
            
            return view("AdminWeb/InformasiPublik/IpDinamisTabel.delete", [
               'IpDinamisTabel' => $ipDinamisTabel
           ]);
        }
        
        try {
            $result = IpDinamisTabelModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'IpDinamis Tabel berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus IpDinamis Tabel');
        }
    }
}