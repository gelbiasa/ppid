<?php

namespace App\Models\HakAkses;

use App\Models\LevelModel;
use App\Models\Log\TransactionModel;
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

    public static function selectData()
    {
        // Dapatkan data level selain SAR dan RPN
        $levels = LevelModel::whereNotIn('level_kode', ['SAR', 'RPN'])->get();

        // Struktur data untuk view
        $levelUsers = [];

        foreach ($levels as $level) {
            $users = UserModel::where('fk_m_level', $level->level_id)->get();
            $menus = self::getMenusByJenisMenu($level->level_kode);

            $levelUsers[$level->level_kode] = [
                'nama' => $level->level_nama,
                'users' => $users,
                'menus' => $menus
            ];
        }

        return self::responFormatSukses($levelUsers, 'Data hak akses berhasil dimuat.');
    }

    public function createData()
    {
        //
    }

    public static function updateData($data, $isLevel = false)
    {
        try {
            DB::beginTransaction();

            // Jika simpan berdasarkan level
            if ($isLevel) {
                $levelKode = $data['level_kode'];
                $menuAkses = $data['menu_akses'] ?? [];

                // Ambil semua pengguna dalam level_kode tertentu
                $users = UserModel::whereHas('level', function ($query) use ($levelKode) {
                    $query->where('level_kode', $levelKode);
                })->pluck('user_id');

                $level = LevelModel::where('level_kode', $levelKode)->first();
                $levelNama = $level ? $level->level_nama : $levelKode;

                // Simpan status hak akses lama untuk dibandingkan nanti
                $oldHakAksesStatus = [];

                // Ambil semua menu_id dari request
                $menuIds = array_keys($menuAkses);

                // Dapatkan status hak akses saat ini untuk semua menu
                foreach ($menuIds as $menu_id) {
                    // Ambil contoh hak akses dari user pertama di level ini (asumsi semua user di level yang sama memiliki hak akses yang sama)
                    if (count($users) > 0) {
                        $user_id = $users[0];
                        $hakAkses = self::where('ha_pengakses', $user_id)
                            ->where('fk_web_menu', $menu_id)
                            ->first();

                        if ($hakAkses) {
                            $oldHakAksesStatus[$menu_id] = [
                                'ha_view' => $hakAkses->ha_view,
                                'ha_create' => $hakAkses->ha_create,
                                'ha_update' => $hakAkses->ha_update,
                                'ha_delete' => $hakAkses->ha_delete
                            ];
                        } else {
                            $oldHakAksesStatus[$menu_id] = [
                                'ha_view' => 0,
                                'ha_create' => 0,
                                'ha_update' => 0,
                                'ha_delete' => 0
                            ];
                        }
                    }
                }

                // Simpan pengaturan untuk setiap menu yang dipilih
                foreach ($menuAkses as $menu_id => $akses) {
                    foreach ($users as $user_id) {
                        $hakAkses = self::firstOrNew([
                            'ha_pengakses' => $user_id,
                            'fk_web_menu' => $menu_id
                        ]);

                        $hakAkses->ha_view = isset($akses['view']) ? 1 : 0;
                        $hakAkses->ha_create = isset($akses['create']) ? 1 : 0;
                        $hakAkses->ha_update = isset($akses['update']) ? 1 : 0;
                        $hakAkses->ha_delete = isset($akses['delete']) ? 1 : 0;
                        $hakAkses->save();
                    }
                }

                // Identifikasi menu yang benar-benar berubah dan buat log transaksi hanya untuk menu tersebut
                foreach ($menuAkses as $menu_id => $akses) {
                    // Periksa apakah hak akses berubah dibandingkan status sebelumnya
                    $oldStatus = $oldHakAksesStatus[$menu_id] ?? [
                        'ha_view' => 0,
                        'ha_create' => 0,
                        'ha_update' => 0,
                        'ha_delete' => 0
                    ];

                    $newStatus = [
                        'ha_view' => isset($akses['view']) ? 1 : 0,
                        'ha_create' => isset($akses['create']) ? 1 : 0,
                        'ha_update' => isset($akses['update']) ? 1 : 0,
                        'ha_delete' => isset($akses['delete']) ? 1 : 0
                    ];

                    // Cek apakah ada perubahan status hak akses
                    $isChanged = false;
                    foreach (['ha_view', 'ha_create', 'ha_update', 'ha_delete'] as $hakType) {
                        if ($oldStatus[$hakType] != $newStatus[$hakType]) {
                            $isChanged = true;
                            break;
                        }
                    }

                    // Jika ada perubahan, buat log
                    if ($isChanged) {
                        $menu = WebMenuModel::find($menu_id);
                        if ($menu) {
                            $detailAktivitas = $menu->wm_menu_nama . " untuk semua " . $levelNama;

                            TransactionModel::createData(
                                'UPDATED',
                                $menu_id,
                                $detailAktivitas
                            );
                        }
                    }
                }

                DB::commit();
                return self::responFormatSukses(null, 'Hak akses berhasil diperbarui untuk level');
            }
            // Jika simpan berdasarkan hak akses individual
            else {
                $hakAksesData = [];
                // Untuk melacak perubahan per user dan per menu
                $userMenuChanges = [];
                // Untuk melacak perubahan status hak akses
                $statusChanges = [];

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

                            // Simpan data untuk melacak perubahan
                            if (!isset($userMenuChanges[$pengakses_id])) {
                                $userMenuChanges[$pengakses_id] = [];
                                $statusChanges[$pengakses_id] = [];
                            }

                            if (!isset($userMenuChanges[$pengakses_id][$menu_id])) {
                                $userMenuChanges[$pengakses_id][$menu_id] = false; // Default: tidak berubah

                                // Ambil status hak akses saat ini
                                $currentHakAkses = self::where('ha_pengakses', $pengakses_id)
                                    ->where('fk_web_menu', $menu_id)
                                    ->first();

                                if (!isset($statusChanges[$pengakses_id][$menu_id])) {
                                    $statusChanges[$pengakses_id][$menu_id] = [
                                        'old' => [
                                            'view' => $currentHakAkses ? $currentHakAkses->ha_view : 0,
                                            'create' => $currentHakAkses ? $currentHakAkses->ha_create : 0,
                                            'update' => $currentHakAkses ? $currentHakAkses->ha_update : 0,
                                            'delete' => $currentHakAkses ? $currentHakAkses->ha_delete : 0
                                        ],
                                        'new' => [
                                            'view' => 0,
                                            'create' => 0,
                                            'update' => 0,
                                            'delete' => 0
                                        ]
                                    ];
                                }
                            }

                            // Simpan data hak akses
                            if (!isset($hakAksesData["$pengakses_id-$menu_id"])) {
                                $hakAksesData["$pengakses_id-$menu_id"] = [
                                    'pengakses_id' => $pengakses_id,
                                    'menu_id' => $menu_id,
                                    'view' => 0,
                                    'create' => 0,
                                    'update' => 0,
                                    'delete' => 0
                                ];
                            }

                            $hakAksesData["$pengakses_id-$menu_id"][$hak] = (int) $value;
                            $statusChanges[$pengakses_id][$menu_id]['new'][$hak] = (int) $value;
                        }
                    }
                }

                // Simpan ke database
                foreach (array_values($hakAksesData) as $item) {
                    $hakAkses = self::firstOrNew([
                        'ha_pengakses' => $item['pengakses_id'],
                        'fk_web_menu' => $item['menu_id']
                    ]);

                    // Pastikan semua hak akses diperbarui meskipun bernilai 0
                    $hakAkses->ha_view = $item['view'] ?? 0;
                    $hakAkses->ha_create = $item['create'] ?? 0;
                    $hakAkses->ha_update = $item['update'] ?? 0;
                    $hakAkses->ha_delete = $item['delete'] ?? 0;

                    $hakAkses->save();
                }

                // Periksa perubahan sebenarnya dan perbarui tracking perubahan
                foreach ($statusChanges as $pengakses_id => $menuStatus) {
                    foreach ($menuStatus as $menu_id => $status) {
                        $isChanged = false;
                        foreach (['view', 'create', 'update', 'delete'] as $hak) {
                            if ($status['old'][$hak] != $status['new'][$hak]) {
                                $isChanged = true;
                                break;
                            }
                        }
                        $userMenuChanges[$pengakses_id][$menu_id] = $isChanged;
                    }
                }

                // Buat log transaksi hanya untuk menu yang benar-benar diubah
                foreach ($userMenuChanges as $user_id => $menuChanges) {
                    $user = UserModel::find($user_id);

                    if ($user) {
                        $nama_pengguna = $user->nama_pengguna;

                        // Untuk setiap menu yang diubah untuk user ini
                        foreach ($menuChanges as $menu_id => $changed) {
                            // Hanya buat log jika benar-benar ada perubahan
                            if ($changed) {
                                $menu = WebMenuModel::find($menu_id);

                                if ($menu) {
                                    // Format detail aktivitas khusus untuk user
                                    $detailAktivitas = $menu->wm_menu_nama . " untuk user " . $nama_pengguna;

                                    TransactionModel::createData(
                                        'UPDATED',
                                        $menu_id,
                                        $detailAktivitas
                                    );
                                }
                            }
                        }
                    }
                }

                DB::commit();
                return self::responFormatSukses(null, 'Hak akses berhasil disimpan');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Terjadi kesalahan saat menyimpan hak akses');
        }
    }

    public function deleteData()
    {
        //
    }

    public function validasiData()
    {
        //
    }

    public static function getHakAksesData($param1, $param2 = null)
    {
        // Jika parameter kedua kosong, berarti ini request untuk hak akses level
        if ($param2 === null) {
            $level_kode = $param1;
            try {
                // Ambil semua menu berdasarkan jenis_menu (level_kode)
                $menus = self::getMenusByJenisMenu($level_kode);

                // Flatten array untuk mendapatkan semua ID menu
                $ambilSemuaMenuId = [];
                foreach ($menus as $category) {
                    if (is_array($category)) {
                        $ambilSemuaMenuId = array_merge($ambilSemuaMenuId, array_values($category));
                    }
                }

                // Ambil hak akses yang sudah tersimpan berdasarkan level
                $hakAkses = self::whereIn('fk_web_menu', $ambilSemuaMenuId)
                    ->select('fk_web_menu', 'ha_view', 'ha_create', 'ha_update', 'ha_delete')
                    ->get()
                    ->keyBy('fk_web_menu');

                // Gabungkan data menu dengan hak akses
                $menuData = [];
                foreach ($menus as $menu_utama => $submenus) {
                    foreach ($submenus as $sub_menu => $menu_id) {
                        $menuData[$menu_id] = [
                            'menu_utama' => $menu_utama,
                            'sub_menu' => $sub_menu === $menu_utama ? null : $sub_menu, // Jika sub_menu sama dengan menu utama, set null
                            'ha_view' => $hakAkses[$menu_id]->ha_view ?? 0,
                            'ha_create' => $hakAkses[$menu_id]->ha_create ?? 0,
                            'ha_update' => $hakAkses[$menu_id]->ha_update ?? 0,
                            'ha_delete' => $hakAkses[$menu_id]->ha_delete ?? 0,
                        ];
                    }
                }

                return $menuData;
            } catch (\Exception $e) {
                return self::responFormatError($e, 'Terjadi kesalahan saat mengambil data hak akses');
            }
        }
        // Jika kedua parameter ada, berarti ini request untuk hak akses spesifik
        else {
            $pengakses_id = $param1;
            $menu_id = $param2;

            return self::where('ha_pengakses', $pengakses_id)
                ->where('fk_web_menu', $menu_id)
                ->first();
        }
    }

    // Method untuk mendapatkan menu berdasarkan jenis_menu (level_kode)
    public static function getMenusByJenisMenu($jenis_menu)
    {
        // Ambil semua parent menu yang aktif dan tidak dihapus
        $parentMenus = WebMenuModel::where('wm_jenis_menu', $jenis_menu)
            ->whereNull('wm_parent_id')
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        $menuStructure = [];

        foreach ($parentMenus as $parentMenu) {
            // Ambil sub-menu yang aktif dan tidak dihapus
            $submenus = WebMenuModel::where('wm_parent_id', $parentMenu->web_menu_id)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->orderBy('wm_urutan_menu')
                ->get();

            if ($submenus->count() > 0) {
                // Jika ada sub-menu, hanya sub-menu yang bisa diedit
                foreach ($submenus as $submenu) {
                    $menuStructure[] = [
                        'menu_utama' => $parentMenu->wm_menu_nama,
                        'sub_menu' => $submenu->wm_menu_nama,
                        'menu_id' => $submenu->web_menu_id
                    ];
                }
            } else {
                // Jika tidak ada sub-menu, maka parent menu bisa diedit
                $menuStructure[] = [
                    'menu_utama' => $parentMenu->wm_menu_nama,
                    'sub_menu' => null,
                    'menu_id' => $parentMenu->web_menu_id
                ];
            }
        }

        $formattedMenuStructure = [];
        foreach ($menuStructure as $menu) {
            $menuUtama = isset($menu['menu_utama']) ? (string)$menu['menu_utama'] : 'Uncategorized'; // Pastikan string
            $subMenu = isset($menu['sub_menu']) ? (string)$menu['sub_menu'] : null; // Pastikan string atau null

            if ($subMenu !== null) {
                $formattedMenuStructure[$menuUtama][$subMenu] = $menu['menu_id'];
            } else {
                $formattedMenuStructure[$menuUtama] = [$menuUtama => $menu['menu_id']];
            }
        }

        return $formattedMenuStructure;
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
