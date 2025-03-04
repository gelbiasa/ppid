<?php

namespace App\Models\HakAkses;

use App\Models\TraitsModel;
use App\Models\UserModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HakAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'm_hak_akses';
    protected $primaryKey = 'hak_akses_id';
    protected $fillable = [
        'fk_web_menu',
        'ha_pengakses',
        'ha_view',
        'ha_create',
        'ha_update',
        'ha_delete'
    ];

    // Relasi ke tabel web_menu
    public function webMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
    }

    // Method untuk mendapatkan semua user berdasarkan level kode
    public static function getUsersByLevelKode($level_kode)
    {
        return UserModel::whereHas('level', function ($query) use ($level_kode) {
            $query->where('level_kode', $level_kode);
        })->get();
    }

    // Method untuk mendapatkan menu berdasarkan jenis_menu (level_kode)
    public static function getMenusByJenisMenu($jenis_menu)
    {
        // Ambil menu induk berdasarkan jenis_menu
        $parentMenus = WebMenuModel::where('wm_jenis_menu', $jenis_menu)
            ->whereNull('wm_parent_id')
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        $menuStructure = [];

        foreach ($parentMenus as $parentMenu) {
            $submenus = WebMenuModel::where('wm_parent_id', $parentMenu->web_menu_id)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->orderBy('wm_urutan_menu')
                ->get();

            $menuStructure[$parentMenu->wm_menu_nama] = [];
            
            foreach ($submenus as $submenu) {
                $menuStructure[$parentMenu->wm_menu_nama][$submenu->wm_menu_nama] = $submenu->web_menu_id;
            }
        }

        return $menuStructure;
    }

    // Method untuk mendapatkan hak akses pengguna untuk menu tertentu
    public static function getHakAkses($pengakses_id, $menu_id)
    {
        return self::where('ha_pengakses', $pengakses_id)
            ->where('fk_web_menu', $menu_id)
            ->first();
    }

    // Method untuk menyimpan atau memperbarui hak akses
    public static function simpanHakAkses($data)
    {
        try {
            DB::beginTransaction();
            
            foreach ($data as $item) {
                $hakAkses = self::where('ha_pengakses', $item['pengakses_id'])
                    ->where('fk_web_menu', $item['menu_id'])
                    ->first();
                
                if (!$hakAkses) {
                    $hakAkses = new self();
                    $hakAkses->ha_pengakses = $item['pengakses_id'];
                    $hakAkses->fk_web_menu = $item['menu_id'];
                }
                
                $hakAkses->ha_view = isset($item['view']) ? 1 : 0;
                $hakAkses->ha_create = isset($item['create']) ? 1 : 0;
                $hakAkses->ha_update = isset($item['update']) ? 1 : 0;
                $hakAkses->ha_delete = isset($item['delete']) ? 1 : 0;
                
                $hakAkses->save();
            }
            
            DB::commit();
            return [
                'success' => true,
                'message' => 'Hak akses berhasil disimpan'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    // Method untuk mengecek apakah user memiliki hak akses tertentu
    public static function cekHakAkses($user_id, $menu_url, $hak)
    {
        // Cek user level
        $user = UserModel::find($user_id);
        
        // Jika user adalah super admin, berikan akses penuh
        if ($user && $user->level->level_kode === 'SAR') {
            return true;
        }
        
        // Temukan menu berdasarkan URL
        $menu = WebMenuModel::where('wm_menu_url', $menu_url)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->first();
            
        if (!$menu) {
            return false;
        }
        
        // Buat prefix untuk kolom hak akses
        $hakField = 'ha_' . $hak;
        
        // Cek hak akses
        $hakAkses = self::where('ha_pengakses', $user_id)
            ->where('fk_web_menu', $menu->web_menu_id)
            ->first();
            
        if (!$hakAkses) {
            return false;
        }
        
        return $hakAkses->$hakField == 1;
    }
}
