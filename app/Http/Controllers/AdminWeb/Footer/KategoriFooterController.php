<?php

namespace App\Http\Controllers\AdminWeb\Footer;

use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class KategoriFooterController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Kategori Footer';
    public $pagename = 'AdminWeb/KategoriFooter';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Kategori Footer',
            'list' => ['Home', 'Pengaturan Kategori Footer']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Footer'
        ];

        $activeMenu = 'kategori-footer';

        $kategoriFooter = KategoriFooterModel::selectData(10, $search);

        return view("AdminWeb/KategoriFooter.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriFooter' => $kategoriFooter,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriFooter = KategoriFooterModel::selectData(10, $search);

        if ($request->ajax()) {
            return view('AdminWeb/KategoriFooter.data', compact('kategoriFooter', 'search'))->render();
        }

        return redirect()->route('kategori-footer.index');
    }

    public function addData()
    {
        return view("AdminWeb/KategoriFooter.create");
    }

    public function createData(Request $request)
    {
        try {
            KategoriFooterModel::validasiData($request);
            $result = KategoriFooterModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Kategori footer berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat kategori footer');
        }
    }

    public function editData($id)
    {
        $kategoriFooter = KategoriFooterModel::detailData($id);

        return view("AdminWeb/KategoriFooter.update", [
            'kategoriFooter' => $kategoriFooter
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {

            KategoriFooterModel::validasiData($request, $id);
            $result = KategoriFooterModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Kategori footer berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui kategori footer');
        }
    }

    public function detailData($id)
    {
        $kategoriFooter = KategoriFooterModel::detailData($id);

        return view("AdminWeb/KategoriFooter.detail", [
            'kategoriFooter' => $kategoriFooter,
            'title' => 'Detail Kategori Footer'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $kategoriFooter = KategoriFooterModel::detailData($id);

            return view("AdminWeb/KategoriFooter.delete", [
                'kategoriFooter' => $kategoriFooter
            ]);
        }

        try {
            $result = KategoriFooterModel::deleteData($id);

            // Periksa apakah operasi berhasil
            if ($result['success'] === false) {
                return $this->jsonError(new \Exception($result['message']), $result['message']);
            }

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Kategori footer berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus kategori footer');
        }
    }
}