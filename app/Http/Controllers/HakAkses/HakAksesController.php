<?php

namespace App\Http\Controllers\HakAkses;

use App\Http\Controllers\TraitsController;
use App\Models\HakAkses\HakAksesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HakAksesController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Hak Akses';
    public $pagename = 'HakAkses';

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Pengaturan Hak Akses',
            'list' => ['Home', 'Hak Akses']
        ];

        $page = (object) [
            'title' => 'Pengaturan Hak Akses'
        ];

        $activeMenu = 'HakAkses';

        // Mengambil data dari model
        $result = HakAksesModel::selectData();
        $levelUsers = $result['data'];

        return view("HakAkses.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'levelUsers' => $levelUsers
        ]);
    }

    public function edit($param1, $param2 = null)
    {
        // Jika param2 tidak ada, maka ini adalah permintaan hak akses berdasarkan level
        if ($param2 === null) {
            $level_kode = $param1;
            $menuData = HakAksesModel::getHakAksesData($level_kode);
            return response()->json($menuData);
        }
        // Jika param2 ada, maka ini adalah permintaan hak akses spesifik user dan menu
        else {
            $pengakses_id = $param1;
            $menu_id = $param2;
            $hakAkses = HakAksesModel::getHakAksesData($pengakses_id, $menu_id);
            return response()->json($hakAkses);
        }
    }

    public function update(Request $request, $isLevel = false)
    {
        try {
            // Menentukan apakah permintaan berasal dari form level atau individual
            // Jika ada ajax request, maka ini adalah permintaan dari form level
            if ($request->ajax() || $isLevel) {
                // Proses data dengan model
                $result = HakAksesModel::updateData($request->all(), true);

                if ($result['success']) {
                    return response()->json(['success' => true, 'message' => $result['message']]);
                } else {
                    return response()->json(['success' => false, 'message' => $result['message']]);
                }
            }
            // Jika bukan ajax request, maka ini adalah permintaan dari form individual
            else {
                // Proses data menggunakan model
                $result = HakAksesModel::updateData($request->all(), false);

                if ($result['success']) {
                    return redirect()->back()->with('success', $result['message']);
                } else {
                    return redirect()->back()->with('error', $result['message']);
                }
            }
        } catch (\Exception $e) {
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            // Jika ajax request, kembalikan response json
            if ($request->ajax() || $isLevel) {
                return response()->json(['success' => false, 'message' => $errorMessage]);
            }
            // Jika bukan ajax request, redirect dengan error message
            else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }
    }
}
