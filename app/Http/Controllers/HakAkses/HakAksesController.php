<?php

namespace App\Http\Controllers\HakAkses;

use App\Http\Controllers\TraitsController;
use App\Models\HakAkses\HakAksesModel;
use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HakAksesController extends Controller
{
    use TraitsController;

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
        
        // Dapatkan data level selain SAR dan RPN
        $levels = LevelModel::whereNotIn('level_kode', ['SAR', 'RPN'])->get();
        
        // Struktur data untuk view
        $levelUsers = [];
        
        foreach ($levels as $level) {
            $users = UserModel::where('fk_m_level', $level->level_id)->get();
            $menus = HakAksesModel::getMenusByJenisMenu($level->level_kode);
            
            $levelUsers[$level->level_kode] = [
                'nama' => $level->level_nama,
                'users' => $users,
                'menus' => $menus
            ];
        }

        return view("HakAkses.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'levelUsers' => $levelUsers
        ]);
    }
    
    public function simpan(Request $request)
    {
        $data = $request->all();
        $hakAksesData = [];
        
        // Proses data dari form
        foreach ($data as $key => $value) {
            if (strpos($key, 'hak_akses_') === 0) {
                // Format key: hak_akses_[pengakses_id]_[menu_id]_[hak]
                $parts = explode('_', $key);
                
                // Pastikan format sesuai: hak_akses_[pengakses_id]_[menu_id]_[hak]
                if (count($parts) >= 5) {
                    $pengakses_id = $parts[2];
                    $menu_id = $parts[3];
                    $hak = end($parts);
                    
                    // Temukan atau buat entry untuk pengakses_id dan menu_id ini
                    $found = false;
                    foreach ($hakAksesData as &$item) {
                        if ($item['pengakses_id'] == $pengakses_id && $item['menu_id'] == $menu_id) {
                            $item[$hak] = true;
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $hakAksesData[] = [
                            'pengakses_id' => $pengakses_id,
                            'menu_id' => $menu_id,
                            $hak => true
                        ];
                    }
                }
            }
        }
        
        $result = HakAksesModel::simpanHakAkses($hakAksesData);
        
        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
    
    public function getHakAkses($pengakses_id, $menu_id)
    {
        $hakAkses = HakAksesModel::getHakAkses($pengakses_id, $menu_id);
        return response()->json($hakAkses);
    }
}
