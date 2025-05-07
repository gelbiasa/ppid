<?php

namespace App\Models\HakAkses;

use App\Models\HakAksesModel;
use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use App\Models\UserModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SetHakAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'set_hak_akses';
    protected $primaryKey = 'set_hak_akses_id';
    protected $fillable = [
        'fk_web_menu',
        'ha_pengakses',
        'ha_menu',
        'ha_view',
        'ha_create',
        'ha_update',
        'ha_delete'
    ];

    // Relasi ke tabel web_menu
    public function WebMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        // Dapatkan data level selain SAR dan RPN
        $levels = HakAksesModel::whereNotIn('hak_akses_kode', ['SAR', 'RPN'])->get();

        // Struktur data untuk view
        $levelUsers = [];

        foreach ($levels as $level) {
            // Ubah cara mendapatkan user berdasarkan level
            $userIds = DB::table('set_user_hak_akses')
                ->where('fk_m_hak_akses', $level->hak_akses_id)
                ->where('isDeleted', 0)
                ->pluck('fk_m_user');

            $users = UserModel::whereIn('user_id', $userIds)->get();
            $menus = self::getMenusByJenisMenu($level->hak_akses_kode);

            $levelUsers[$level->hak_akses_kode] = [
                'nama' => $level->hak_akses_nama,
                'users' => $users,
                'menus' => $menus
            ];
        }

        return self::responFormatSukses($levelUsers, 'Data hak akses berhasil dimuat.');
    }

    public static function updateData($data, $isLevel = false)
    {
        try {
            DB::beginTransaction();

            // Jika simpan berdasarkan level
            if ($isLevel) {
                $hakAksesKode = $data['hak_akses_kode'] ?? null;

                // Ambil level berdasarkan kode
                $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)->first();

                if (!$level) {
                    throw new \Exception('Level tidak ditemukan');
                }

                // Ambil user dengan level tersebut
                $userIds = DB::table('set_user_hak_akses')
                    ->join('m_user', 'set_user_hak_akses.fk_m_user', '=', 'm_user.user_id')
                    ->where('set_user_hak_akses.fk_m_hak_akses', $level->hak_akses_id)
                    ->where('set_user_hak_akses.isDeleted', 0)
                    ->where('m_user.isDeleted', 0)
                    ->pluck('m_user.user_id');

                if ($userIds->isEmpty()) {
                    throw new \Exception('Tidak ada pengguna dengan level ini');
                }

                // Proses setiap menu yang dipilih
                $menuAkses = $data['menu_akses'] ?? [];

                foreach ($menuAkses as $menu_id => $akses) {
                    // Cari menu yang sesuai
                    $menu = WebMenuModel::find($menu_id);

                    if (!$menu) {
                        continue;
                    }

                    // Tentukan nilai hak akses baru
                    $newHakAkses = [
                        'ha_menu' => isset($akses['menu']) ? 1 : 0,
                        'ha_view' => isset($akses['view']) ? 1 : 0,
                        'ha_create' => isset($akses['create']) ? 1 : 0,
                        'ha_update' => isset($akses['update']) ? 1 : 0,
                        'ha_delete' => isset($akses['delete']) ? 1 : 0
                    ];

                    // Update atau buat hak akses untuk setiap user
                    foreach ($userIds as $userId) {
                        $hakAkses = self::firstOrNew([
                            'ha_pengakses' => $userId,
                            'fk_web_menu' => $menu_id
                        ]);

                        $hakAkses->ha_menu = $newHakAkses['ha_menu'];
                        $hakAkses->ha_view = $newHakAkses['ha_view'];
                        $hakAkses->ha_create = $newHakAkses['ha_create'];
                        $hakAkses->ha_update = $newHakAkses['ha_update'];
                        $hakAkses->ha_delete = $newHakAkses['ha_delete'];

                        // Set created_by jika record baru
                        if (!$hakAkses->exists) {
                            $hakAkses->created_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        } else {
                            $hakAkses->updated_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        }

                        $hakAkses->save();
                    }
                }

                // Catat log transaksi
                try {
                    TransactionModel::createData(
                        'UPDATED',
                        $level->hak_akses_id,
                        "Pengaturan hak akses untuk level {$level->hak_akses_nama}"
                    );
                } catch (\Exception $e) {
                    // Lanjutkan proses meski log gagal
                }

                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Hak akses berhasil diperbarui untuk level'
                ];
            }
            // Jika simpan berdasarkan hak akses individual
            else {
                $userMenuChanges = [];
                $aktivitasLog = [];

                // Proses data dari form
                foreach ($data as $key => $value) {
                    if (strpos($key, 'set_hak_akses_') === 0) {
                        $parts = explode('_', $key);

                        // Pastikan format sesuai
                        if (count($parts) >= 5 && is_numeric($parts[3]) && is_numeric($parts[4])) {
                            $pengakses_id = (int)$parts[3];
                            $menu_id = (int)$parts[4];
                            $hak = $parts[5]; // menu, view, create, update, delete

                            // Inisialisasi tracking perubahan
                            if (!isset($userMenuChanges[$pengakses_id])) {
                                $userMenuChanges[$pengakses_id] = [];
                            }

                            if (!isset($userMenuChanges[$pengakses_id][$menu_id])) {
                                // Get existing permissions
                                $existingPerms = self::where('ha_pengakses', $pengakses_id)
                                    ->where('fk_web_menu', $menu_id)
                                    ->first();

                                // Initialize with existing values or defaults
                                $userMenuChanges[$pengakses_id][$menu_id] = [
                                    'menu' => $existingPerms ? $existingPerms->ha_menu : 0,
                                    'view' => $existingPerms ? $existingPerms->ha_view : 0,
                                    'create' => $existingPerms ? $existingPerms->ha_create : 0,
                                    'update' => $existingPerms ? $existingPerms->ha_update : 0,
                                    'delete' => $existingPerms ? $existingPerms->ha_delete : 0,
                                    'changed' => false
                                ];
                            }

                            // Store the old value for comparison
                            $oldValue = $userMenuChanges[$pengakses_id][$menu_id][$hak];

                            // Update nilai hak akses
                            $userMenuChanges[$pengakses_id][$menu_id][$hak] = (int)$value;

                            // Check if this field has changed
                            if ($oldValue != (int)$value) {
                                $userMenuChanges[$pengakses_id][$menu_id]['changed'] = true;
                            }
                        }
                    }
                }

                // Simpan perubahan ke database - hanya untuk yang berubah
                foreach ($userMenuChanges as $pengakses_id => $menuChanges) {
                    foreach ($menuChanges as $menu_id => $hakAksesData) {
                        // Skip if no changes
                        if (!$hakAksesData['changed']) {
                            continue;
                        }

                        // Remove the tracking flag before saving
                        unset($hakAksesData['changed']);

                        // Cari atau buat record hak akses
                        $hakAkses = self::firstOrNew([
                            'ha_pengakses' => $pengakses_id,
                            'fk_web_menu' => $menu_id
                        ]);

                        // Update nilai hak akses
                        $hakAkses->ha_menu = $hakAksesData['menu'];
                        $hakAkses->ha_view = $hakAksesData['view'];
                        $hakAkses->ha_create = $hakAksesData['create'];
                        $hakAkses->ha_update = $hakAksesData['update'];
                        $hakAkses->ha_delete = $hakAksesData['delete'];

                        // Set created_by atau updated_by
                        if (!$hakAkses->exists) {
                            $hakAkses->created_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        } else {
                            $hakAkses->updated_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        }

                        $hakAkses->save();

                        // Ambil detail menu dan user untuk log
                        $menu = WebMenuModel::find($menu_id);
                        $user = UserModel::find($pengakses_id);

                        if ($menu && $user) {
                            $menuName = $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : 'Menu');

                            $detailAktivitas = "Perbarui hak akses {$menuName} untuk pengguna {$user->nama_pengguna}";
                            $aktivitasLog[] = [
                                'menu_id' => $menu_id,
                                'aktivitas' => $detailAktivitas
                            ];
                        }
                    }
                }

                // Only log transaction if there were actual changes
                if (!empty($aktivitasLog)) {
                    try {
                        // Dapatkan data set_user_hak_akses_id untuk setiap user yang haknya diubah
                        foreach ($userMenuChanges as $pengakses_id => $menuChanges) {
                            // Cek apakah pengguna ini memiliki perubahan hak akses
                            $hasChanges = false;
                            foreach ($menuChanges as $menu_id => $hakAksesData) {
                                if (isset($hakAksesData['changed']) && $hakAksesData['changed']) {
                                    $hasChanges = true;
                                    break;
                                }
                            }

                            if ($hasChanges) {
                                // Ambil user dari database
                                $user = UserModel::find($pengakses_id);

                                if ($user) {
                                    // Dapatkan set_user_hak_akses_id berdasarkan user_id
                                    $userHakAkses = DB::table('set_user_hak_akses')
                                        ->where('fk_m_user', $pengakses_id)
                                        ->where('isDeleted', 0)
                                        ->first();

                                    if ($userHakAkses) {
                                        // Buat detail aktivitas yang lebih spesifik
                                        $detailAktivitas = "Perbarui pengaturan hak akses untuk pengguna {$user->nama_pengguna}";

                                        // Log transaksi dengan set_user_hak_akses_id sebagai aktivitasId
                                        TransactionModel::createData(
                                            'UPDATED',
                                            $userHakAkses->set_user_hak_akses_id, // Gunakan set_user_hak_akses_id pengguna
                                            $detailAktivitas
                                        );
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Lanjutkan proses meski log gagal
                        Log::error('Gagal mencatat transaksi: ' . $e->getMessage());
                    }
                }

                DB::commit();
                return [
                    'success' => true,
                    'message' => !empty($aktivitasLog) ?
                        'Hak akses berhasil disimpan untuk ' . count($aktivitasLog) . ' menu' :
                        'Tidak ada perubahan hak akses yang dilakukan'
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan hak akses: ' . $e->getMessage()
            ];
        }
    }


    public static function getHakAksesData($param1, $param2 = null)
    {
        // Jika parameter kedua kosong, ini request untuk hak akses level
        if ($param2 === null) {
            $hak_akses_kode = $param1;
            try {
                // Ambil level berdasarkan kode
                $level = HakAksesModel::where('hak_akses_kode', $hak_akses_kode)->first();
                if (!$level) return [];

                // Ambil semua menu untuk level ini
                $menus = self::getMenusByJenisMenu($hak_akses_kode);

                // Ambil user pertama dengan level ini untuk cek hak akses
                $userIds = DB::table('set_user_hak_akses')
                    ->where('fk_m_hak_akses', $level->hak_akses_id)
                    ->where('isDeleted', 0)
                    ->limit(1)
                    ->pluck('fk_m_user');

                $firstUserId = $userIds->first();

                // Gabungkan data menu dengan hak akses
                $menuData = [];
                foreach ($menus as $menu_utama => $submenus) {
                    foreach ($submenus as $sub_menu => $menu_id) {
                        // Cari menu berdasarkan ID
                        $menu = WebMenuModel::find($menu_id);
                        if (!$menu) continue;

                        // Ambil hak akses yang sudah tersimpan (dari user pertama)
                        $hakAkses = null;
                        if ($firstUserId) {
                            $hakAkses = self::where('ha_pengakses', $firstUserId)
                                ->where('fk_web_menu', $menu_id)
                                ->first();
                        }

                        $menuData[$menu_id] = [
                            'menu_utama' => $menu_utama,
                            'sub_menu' => $sub_menu === $menu_utama ? null : $sub_menu,
                            'ha_menu' => $hakAkses ? $hakAkses->ha_menu : 0,
                            'ha_view' => $hakAkses ? $hakAkses->ha_view : 0,
                            'ha_create' => $hakAkses ? $hakAkses->ha_create : 0,
                            'ha_update' => $hakAkses ? $hakAkses->ha_update : 0,
                            'ha_delete' => $hakAkses ? $hakAkses->ha_delete : 0,
                        ];
                    }
                }

                return $menuData;
            } catch (\Exception $e) {
                Log::error('Error getting hak akses data: ' . $e->getMessage());
                return [];
            }
        }
        // Jika kedua parameter ada, ini request hak akses spesifik
        else {
            $pengakses_id = $param1;
            $menu_id = $param2;

            // Cari hak akses berdasarkan user dan menu
            $hakAkses = self::where('ha_pengakses', $pengakses_id)
                ->where('fk_web_menu', $menu_id)
                ->first();

            // Jika tidak ditemukan, kembalikan nilai default
            if (!$hakAkses) {
                return [
                    'ha_menu' => 0,
                    'ha_view' => 0,
                    'ha_create' => 0,
                    'ha_update' => 0,
                    'ha_delete' => 0
                ];
            }

            return $hakAkses;
        }
    }

    // Method untuk mendapatkan menu berdasarkan jenis_menu (hak_akses_kode)
    public static function getMenusByJenisMenu($hak_akses_kode)
    {
        // Ambil level berdasarkan kode level
        $level = HakAksesModel::where('hak_akses_kode', $hak_akses_kode)->first();

        if (!$level) {
            return [];
        }

        // Ambil semua parent menu untuk level ini
        $parentMenus = WebMenuModel::where('fk_m_hak_akses', $level->hak_akses_id)
            ->whereNull('wm_parent_id')
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        $menuStructure = [];

        foreach ($parentMenus as $parentMenu) {
            // Ambil sub-menu yang aktif
            $submenus = WebMenuModel::where('wm_parent_id', $parentMenu->web_menu_id)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->orderBy('wm_urutan_menu')
                ->get();

            if ($submenus->count() > 0) {
                // Jika ada sub-menu
                foreach ($submenus as $submenu) {
                    $menuStructure[] = [
                        'menu_utama' => $parentMenu->wm_menu_nama ?: ($parentMenu->WebMenuGlobal ? $parentMenu->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu'),
                        'sub_menu' => $submenu->wm_menu_nama ?: ($submenu->WebMenuGlobal ? $submenu->WebMenuGlobal->wmg_nama_default : 'Unnamed Submenu'),
                        'menu_id' => $submenu->web_menu_id
                    ];
                }
            } else {
                // Jika tidak ada sub-menu
                $menuStructure[] = [
                    'menu_utama' => $parentMenu->wm_menu_nama ?: ($parentMenu->WebMenuGlobal ? $parentMenu->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu'),
                    'sub_menu' => null,
                    'menu_id' => $parentMenu->web_menu_id
                ];
            }
        }

        // Format struktur menu
        $formattedMenuStructure = [];
        foreach ($menuStructure as $menu) {
            $menuUtama = isset($menu['menu_utama']) ? (string)$menu['menu_utama'] : 'Uncategorized';
            $subMenu = isset($menu['sub_menu']) ? (string)$menu['sub_menu'] : null;

            if ($subMenu !== null) {
                $formattedMenuStructure[$menuUtama][$subMenu] = $menu['menu_id'];
            } else {
                $formattedMenuStructure[$menuUtama] = [$menuUtama => $menu['menu_id']];
            }
        }

        return $formattedMenuStructure;
    }

    public static function cekHakAksesMenu($user_id, $menu_url)
    {
        try {
            // Cek user
            $user = UserModel::find($user_id);
            if (!$user) return false;

            // Ambil hak akses aktif user
            $activeHakAksesId = session('active_hak_akses_id');
            $hakAkses = null;

            if ($activeHakAksesId) {
                $hakAkses = DB::table('m_hak_akses')
                    ->where('hak_akses_id', $activeHakAksesId)
                    ->where('isDeleted', 0)
                    ->first();
            } else {
                // Jika belum ada hak akses aktif, ambil yang pertama
                $hakAkses = DB::table('set_user_hak_akses')
                    ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
                    ->where('set_user_hak_akses.fk_m_user', $user_id)
                    ->where('set_user_hak_akses.isDeleted', 0)
                    ->where('m_hak_akses.isDeleted', 0)
                    ->first();

                if ($hakAkses) {
                    session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
                }
            }

            // Jika super admin, berikan akses penuh
            if ($hakAkses && $hakAkses->hak_akses_kode === 'SAR') {
                return true;
            }

            $hakAksesId = $hakAkses ? $hakAkses->hak_akses_id : null;

            // Cari menu berdasarkan URL
            $menu = WebMenuModel::whereHas('WebMenuUrl', function ($query) use ($menu_url) {
                $query->where('wmu_nama', $menu_url);
            })
                ->where('fk_m_hak_akses', $hakAksesId)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->first();

            // Jika tidak ditemukan, cari menu dengan URL yang sama tanpa mempedulikan level
            if (!$menu) {
                $menu = WebMenuModel::whereHas('WebMenuUrl', function ($query) use ($menu_url) {
                    $query->where('wmu_nama', $menu_url);
                })
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->first();
            }

            if (!$menu) {
                return false;
            }

            // Cek hak akses menu
            $hakAkses = self::where('ha_pengakses', $user_id)
                ->where('fk_web_menu', $menu->web_menu_id)
                ->first();

            if (!$hakAkses) {
                return false;
            }
            return $hakAkses->ha_menu == 1;
        } catch (\Exception $e) {
            // Log error dan return default value
            Log::error('Error in cekHakAksesMenu: ' . $e->getMessage());
            return false; // Atau return true jika Anda ingin memberikan akses default
        }
    }

    public static function cekHakAkses($user_id, $menu_url, $hak)
    {
        try {
            // Cek user
            $user = UserModel::find($user_id);
            if (!$user) return false;

            // Ambil hak akses aktif user
            $activeHakAksesId = session('active_hak_akses_id');
            $hakAkses = null;

            if ($activeHakAksesId) {
                $hakAkses = DB::table('m_hak_akses')
                    ->where('hak_akses_id', $activeHakAksesId)
                    ->where('isDeleted', 0)
                    ->first();
            } else {
                // Jika belum ada hak akses aktif, ambil yang pertama
                $hakAkses = DB::table('set_user_hak_akses')
                    ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
                    ->where('set_user_hak_akses.fk_m_user', $user_id)
                    ->where('set_user_hak_akses.isDeleted', 0)
                    ->where('m_hak_akses.isDeleted', 0)
                    ->first();

                if ($hakAkses) {
                    session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
                }
            }

            // Jika super admin, berikan akses penuh
            if ($hakAkses && $hakAkses->hak_akses_kode === 'SAR') {
                return true;
            }

            $hakAksesId = $hakAkses ? $hakAkses->hak_akses_id : null;

            // Cari menu berdasarkan URL
            $menu = WebMenuModel::whereHas('WebMenuUrl', function ($query) use ($menu_url) {
                $query->where('wmu_nama', $menu_url);
            })
                ->where('fk_m_hak_akses', $hakAksesId)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->first();

            // Jika tidak ditemukan, cari menu dengan URL yang sama tanpa mempedulikan level
            if (!$menu) {
                $menu = WebMenuModel::whereHas('WebMenuUrl', function ($query) use ($menu_url) {
                    $query->where('wmu_nama', $menu_url);
                })
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->first();
            }

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
        } catch (\Exception $e) {
            // Log error dan return default value
            Log::error('Error in cekHakAkses: ' . $e->getMessage());
            return false; // Atau return true jika Anda ingin memberikan akses default
        }
    }
}
