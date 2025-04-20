<?php

namespace App\Http\Controllers\AdminWeb\Footer;

use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\FooterModel;
use App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class FooterController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Footer';
    public $pagename = 'AdminWeb/Footer';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Footer',
            'list' => ['Home', 'Pengaturan Footer']
        ];

        $page = (object) [
            'title' => 'Daftar Footer'
        ];

        $activeMenu = 'footer';
        $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
        
        $footer = FooterModel::selectData(10, $search);

        return view("AdminWeb/Footer.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriFooters' => $kategoriFooters,
            'footer' => $footer,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $footer = FooterModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('AdminWeb/Footer.data', compact('footer', 'search'))->render();
        }
        
        return redirect()->route('footer.index');
    }

    public function addData()
    {
        $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
        return view("AdminWeb/Footer.create", compact('kategoriFooters'));
    }

    public function createData(Request $request)
    {
        try {
            FooterModel::validasiData($request);
            $result = FooterModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Footer berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat footer');
        }
    }

    public function editData($id)
    {
        $footer = FooterModel::detailData($id);
        $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();

        return view("AdminWeb/Footer.update", [
            'footer' => $footer,
            'kategoriFooters' => $kategoriFooters
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            FooterModel::validasiData($request, $id);
            $result = FooterModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Footer berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui footer');
        }
    }

    public function detailData($id)
    {
        $footer = FooterModel::detailData($id);
        
        return view("AdminWeb/Footer.detail", [
            'footer' => $footer,
            'title' => 'Detail Footer'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $footer = FooterModel::detailData($id);
            
            return view("AdminWeb/Footer.delete", [
                'footer' => $footer
            ]);
        }
        
        try {
            $result = FooterModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Footer berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus footer');
        }
    }
}