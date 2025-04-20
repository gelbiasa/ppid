<?php

namespace App\Http\Controllers\ManagePengguna;

use App\Http\Controllers\TraitsController;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LevelController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Level';
    public $pagename = 'ManagePengguna';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Level',
            'list' => ['Home', 'Pengaturan Level']
        ];

        $page = (object) [
            'title' => 'Daftar Level'
        ];

        $activeMenu = 'managementlevel';

        // Gunakan pagination dan pencarian
        $level = LevelModel::selectData(10, $search);

        return view("ManagePengguna/ManageLevel.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'level' => $level,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pagination dan pencarian
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $level = LevelModel::selectData(10, $search);

        if ($request->ajax()) {
            return view('ManagePengguna/ManageLevel.data', compact('level', 'search'))->render();
        }

        return redirect()->route('level.index');
    }

    public function addData()
    {
        try {
            return view("ManagePengguna/ManageLevel.create");
        } catch (\Exception $e) {
            Log::error('Add Data Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createData(Request $request)
    {
        try {
            LevelModel::validasiData($request);
            $result = LevelModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat level');
        }
    }

    public function editData($id)
    {
        $level = LevelModel::detailData($id);

        return view("ManagePengguna/ManageLevel.update", [
            'level' => $level
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            LevelModel::validasiData($request);
            $result = LevelModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui level');
        }
    }

    public function detailData($id)
    {
        $level = LevelModel::detailData($id);

        return view("ManagePengguna/ManageLevel.detail", [
            'level' => $level,
            'title' => 'Detail Level'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $level = LevelModel::detailData($id);

            return view("ManagePengguna/ManageLevel.delete", [
                'level' => $level
            ]);
        }

        try {
            $result = LevelModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus level');
        }
    }
}
