<?php

namespace App\Http\Controllers\SistemInformasi\Timeline;

use App\Http\Controllers\TraitsController;
use App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use App\Models\SistemInformasi\Timeline\TimelineModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class TimelineController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Timeline';
    public $pagename = 'SistemInformasi/Timeline';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Timeline',
            'list' => ['Home', 'Pengaturan Timeline']
        ];

        $page = (object) [
            'title' => 'Daftar Timeline'
        ];

        $activeMenu = 'Timeline';
        
        // Gunakan pagination dan pencarian
        $timeline = TimelineModel::selectData(10, $search);

        return view("SistemInformasi/Timeline.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'timeline' => $timeline,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pagination dan pencarian
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $timeline = TimelineModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('SistemInformasi/Timeline.data', compact('timeline', 'search'))->render();
        }
        
        return redirect()->route('timeline.index');
    }

    public function addData()
    {
        // Ambil data kategori form dari database
        $TimelineKategoriForm = KategoriFormModel::where('isDeleted', 0)->get();

        return view("SistemInformasi/Timeline.create", [
            'TimelineKategoriForm' => $TimelineKategoriForm
        ]);
    }

    public function createData(Request $request)
    {
        try {
            TimelineModel::validasiData($request);
            $result = TimelineModel::createData($request);

            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Timeline berhasil dibuat'
            );
        } catch (ValidationException $e) {
            // Menggunakan method jsonValidationError dari BaseControllerFunction
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat timeline');
        }
    }

    public function editData($id)
    {
        // Ambil data kategori form dari database
        $TimelineKategoriForm = KategoriFormModel::where('isDeleted', 0)->get();

        // Ambil data timeline yang akan diedit
        $timeline = TimelineModel::with('langkahTimeline')->findOrFail($id);
        $jumlahLangkah = $timeline->langkahTimeline->count();

        return view("SistemInformasi/Timeline.update", [
            'TimelineKategoriForm' => $TimelineKategoriForm,
            'timeline' => $timeline,
            'jumlahLangkah' => $jumlahLangkah
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            // Manipulasi request untuk mengatasi masalah validasi
            $this->preprocessRequest($request);
            
            TimelineModel::validasiData($request);
            $result = TimelineModel::updateData($request, $id);

            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Timeline berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            // Menggunakan method jsonValidationError dari BaseControllerFunction
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui timeline');
        }
    }

    public function detailData($id)
    {
        $timeline = TimelineModel::detailData($id);
        
        return view("SistemInformasi/Timeline.detail", [
            'timeline' => $timeline,
            'title' => 'Detail Timeline'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $timeline = TimelineModel::detailData($id);
            
            return view("SistemInformasi/Timeline.delete", [
                'timeline' => $timeline
            ]);
        }
        
        try {
            $result = TimelineModel::deleteData($id);
            
            // Menggunakan method jsonSuccess dari BaseControllerFunction
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Timeline berhasil dihapus'
            );
        } catch (\Exception $e) {
            // Menggunakan method jsonError dari BaseControllerFunction
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus timeline');
        }
    }

    private function preprocessRequest(Request $request)
    {
        $deletedIndices = [];
        if ($request->has('deleted_indices')) {
            $deletedIndices = json_decode($request->deleted_indices, true) ?? [];
        }
        
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^deleted_step_(\d+)$/', $key, $matches)) {
                $deletedIndices[] = (int)$matches[1];
            }
        }
        
        foreach ($deletedIndices as $index) {
            $request->request->remove("langkah_timeline_$index");
        }
        
        if (count($deletedIndices) > 0 && $request->jumlah_langkah_timeline == 0) {
            $request->merge(['jumlah_langkah_timeline' => 1]);
        }
    }
}