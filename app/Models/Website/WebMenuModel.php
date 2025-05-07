<?php

namespace App\Models\Website;

use App\Models\HakAkses\SetHakAksesModel;
use App\Models\HakAksesModel;
use App\Models\Log\NotifAdminModel;
use App\Models\Log\NotifVerifikatorModel;
use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use App\Models\UserModel;
use App\Models\WebMenuGlobalModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Website\WebKontenModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebMenuModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu';
    protected $primaryKey = 'web_menu_id';

    protected $fillable = [
        'fk_web_menu_global',
        'fk_m_hak_akses',
        'wm_parent_id',
        'wm_urutan_menu',
        'wm_menu_nama',
        'wm_status_menu'
    ];

    // Relationships
    public function parentMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function WebMenuGlobal()
    {
        return $this->belongsTo(WebMenuGlobalModel::class, 'fk_web_menu_global', 'web_menu_global_id');
    }

    public function Level()
    {
        return $this->belongsTo(HakAksesModel::class, 'fk_m_hak_akses', 'hak_akses_id');
    }

    public function WebMenuUrl()
    {
        return $this->hasOneThrough(
            WebMenuUrlModel::class,
            WebMenuGlobalModel::class,
            'web_menu_global_id', // Kunci asing pada WebMenuGlobal
            'web_menu_url_id',    // Kunci utama pada WebMenuUrl
            'fk_web_menu_global', // Kunci untuk menghubungkan WebMenu dengan WebMenuGlobal
            'fk_web_menu_url'     // Kunci untuk menghubungkan WebMenuGlobal dengan WebMenuUrl
        );
    }

    public function children()
    {
        return $this->hasMany(WebMenuModel::class, 'wm_parent_id', 'web_menu_id')
            ->orderBy('wm_urutan_menu');
    }

    public function konten()
    {
        return $this->hasOne(WebKontenModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function hakAkses()
    {
        return $this->hasMany(SetHakAksesModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function getJenisMenuList()
    {
        // Dapatkan daftar jenis menu dari tabel level
        $levels = HakAksesModel::where('isDeleted', 0)->get();
        $jenisMenuList = [];

        foreach ($levels as $level) {
            $jenisMenuList[$level->hak_akses_kode] = $level->hak_akses_nama;
        }

        return $jenisMenuList;
    }

    public static function getDataMenu()
    {
        // Ambil data menu berdasarkan level 'RPN'
        $levelRPN = DB::table('m_hak_akses')->where('hak_akses_kode', 'RPN')->first();

        if (!$levelRPN) {
            return [];
        }

        $arr_data = self::query()
            ->select('web_menu.*')
            ->where('fk_m_hak_akses', $levelRPN->hak_akses_id)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                // Ambil data menu global terkait
                $menuGlobal = DB::table('web_menu_global')
                    ->where('web_menu_global_id', $menu->fk_web_menu_global)
                    ->where('isDeleted', 0)
                    ->first();

                // Tentukan nama menu - prioritaskan wm_menu_nama, jika null gunakan nama dari menu global
                $menuName = $menu->wm_menu_nama;
                if ($menuName === null && $menuGlobal) {
                    $menuName = $menuGlobal->wmg_nama_default;
                }

                // Ambil data URL dari web_menu_url melalui web_menu_global
                $menuUrl = null;
                if ($menuGlobal && $menuGlobal->fk_web_menu_url) {
                    $menuUrlRecord = DB::table('web_menu_url')
                        ->where('web_menu_url_id', $menuGlobal->fk_web_menu_url)
                        ->where('isDeleted', 0)
                        ->first();

                    if ($menuUrlRecord) {
                        $menuUrl = $menuUrlRecord->wmu_nama;
                    }
                }

                // Ambil submenu (anak menu)
                $submenuItems = self::query()
                    ->where('wm_parent_id', $menu->web_menu_id)
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->orderBy('wm_urutan_menu')
                    ->get();

                $submenu = [];
                foreach ($submenuItems as $submenuItem) {
                    // Ambil data menu global untuk submenu
                    $submenuGlobal = DB::table('web_menu_global')
                        ->where('web_menu_global_id', $submenuItem->fk_web_menu_global)
                        ->where('isDeleted', 0)
                        ->first();

                    // Tentukan nama submenu
                    $submenuName = $submenuItem->wm_menu_nama;
                    if ($submenuName === null && $submenuGlobal) {
                        $submenuName = $submenuGlobal->wmg_nama_default;
                    }

                    // Ambil URL submenu
                    $submenuUrl = null;
                    if ($submenuGlobal && $submenuGlobal->fk_web_menu_url) {
                        $submenuUrlRecord = DB::table('web_menu_url')
                            ->where('web_menu_url_id', $submenuGlobal->fk_web_menu_url)
                            ->where('isDeleted', 0)
                            ->first();

                        if ($submenuUrlRecord) {
                            $submenuUrl = $submenuUrlRecord->wmu_nama;
                        }
                    }

                    $submenu[] = [
                        'wm_menu_nama' => $submenuName,
                        'wm_menu_url' => $submenuUrl
                    ];
                }

                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'wm_menu_nama' => $menuName, // Sekarang diisi dengan nilai yang benar
                    'wm_menu_url' => $menuUrl,   // Sekarang diisi dengan URL yang benar
                    'children' => $submenu
                ];
            })->toArray();

        return $arr_data;
    }


    public static function selectBeritaPengumuman()
    {
        $arr_data = self::query()
            ->select([
                'web_menu_id',
                'wm_parent_id',
                'fk_web_menu_global',
                'wm_menu_nama',
                'wm_urutan_menu'
            ])
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereIn('wm_menu_nama', ['Berita', 'Pengumuman'])
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                // Dapatkan nama menu dari web_menu_global jika wm_menu_nama kosong
                $menuName = $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : null);
                // Dapatkan URL dari relasi
                $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : null;

                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_menu_url' => $menuUrl,
                    'wm_menu_nama' => $menuName,
                    'wm_urutan_menu' => $menu->wm_urutan_menu
                ];
            })->toArray();
        return $arr_data;
    }

    public static function mengecekKetersediaanMenu($menuName, $hakAksesId, $excludeId = null)
    {
        $query = self::where('fk_m_hak_akses', $hakAksesId)
            ->where('wm_menu_nama', $menuName)
            ->where('isDeleted', 0);

        if ($excludeId) {
            $query->where('web_menu_id', '!=', $excludeId);
        }

        $menuAktif = clone $query;
        $menuAktif = $menuAktif->where('wm_status_menu', 'aktif')->first();

        if ($menuAktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada dan berstatus aktif untuk level ini'
            ];
        }

        $menuNonaktif = clone $query;
        $menuNonaktif = $menuNonaktif->where('wm_status_menu', 'nonaktif')->first();

        if ($menuNonaktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada, tetapi saat ini berstatus nonaktif untuk level ini, silakan aktifkan menu ini'
            ];
        }

        return [
            'exists' => false
        ];
    }

    public static function createData($request)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);
            $data = $request->web_menu;

            // Validasi apakah user mencoba membuat menu dengan level SAR
            $hakAksesId = $data['fk_m_hak_akses'] ?? null;
            $level = HakAksesModel::find($hakAksesId);

            // Jika level adalah SAR dan user bukan SAR, tolak permintaan
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat menambahkan menu SAR'
                ];
            }

            // Validasi data yang dikirim
            $hakAksesId = $data['fk_m_hak_akses'] ?? null;
            $menuName = $data['wm_menu_nama'];
            $menuUrl = $data['fk_web_menu_url'] ?? null;

            // Cek ketersediaan menu dengan nama yang sama pada level yang sama
            $menuCheck = self::mengecekKetersediaanMenu($menuName, $hakAksesId);
            if ($menuCheck['exists']) {
                return [
                    'success' => false,
                    'message' => $menuCheck['message']
                ];
            }

            // Fix untuk Masalah 3
            // Cek apakah sudah ada menu dengan URL yang sama pada level lain
            $webMenuGlobal = null;
            if ($menuUrl) {
                // Cari WebMenuGlobal dengan URL yang sama
                $webMenuGlobal = WebMenuGlobalModel::where('fk_web_menu_url', $menuUrl)
                    ->where('isDeleted', 0)
                    ->first();

                if ($webMenuGlobal) {
                    // Jika nama yang diinputkan sama dengan wmg_nama_default dan URL sama
                    if ($menuName === $webMenuGlobal->wmg_nama_default) {
                        // Set wm_menu_nama ke null
                        $data['wm_menu_nama'] = null;
                    }
                    // Jika URL sama tapi nama berbeda, pertahankan nama yang diinputkan
                } else {
                    // Jika URL baru, buat WebMenuGlobal baru
                    $webMenuGlobal = WebMenuGlobalModel::create([
                        'fk_web_menu_url' => $menuUrl,
                        'wmg_nama_default' => $menuName
                    ]);

                    // Set wm_menu_nama ke null karena ini adalah menu baru
                    $data['wm_menu_nama'] = null;
                }

                // Set WebMenuGlobal ID
                $data['fk_web_menu_global'] = $webMenuGlobal->web_menu_global_id;
            } else {
                // Fix untuk Masalah 4
                // Jika tidak ada URL, cari atau buat WebMenuGlobal baru untuk nama menu
                $webMenuGlobal = WebMenuGlobalModel::where('wmg_nama_default', $menuName)
                    ->whereNull('fk_web_menu_url')
                    ->where('isDeleted', 0)
                    ->first();

                if (!$webMenuGlobal) {
                    // Buat baru jika belum ada
                    $webMenuGlobal = WebMenuGlobalModel::create([
                        'fk_web_menu_url' => null,
                        'wmg_nama_default' => $menuName
                    ]);
                }

                $data['fk_web_menu_global'] = $webMenuGlobal->web_menu_global_id;
                // Set wm_menu_nama ke null karena menggunakan nama default dari WebMenuGlobal
                $data['wm_menu_nama'] = null;
            }

            // Tambahkan urutan menu
            $data['wm_urutan_menu'] = self::where('wm_parent_id', $data['wm_parent_id'] ?? null)
                ->where('isDeleted', 0)
                ->count() + 1;

            // Buat menu baru
            $saveData = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama ?: ($saveData->WebMenuGlobal ? $saveData->WebMenuGlobal->wmg_nama_default : 'Menu Baru')
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Menu berhasil dibuat',
                'data' => $saveData
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat menu: ' . $e->getMessage()
            ];
        }
    }

    public static function updateData($request, $id)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);

            $saveData = self::findOrFail($id);
            $data = $request->web_menu;

            // Periksa level menu
            $level = HakAksesModel::find($saveData->fk_m_hak_akses);
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR'
                ];
            }

            // Simpan nilai lama untuk perbandingan
            $oldhakAksesId = $saveData->fk_m_hak_akses;
            $newhakAksesId = $data['fk_m_hak_akses'] ?? $oldhakAksesId;
            $menuName = $data['wm_menu_nama'];
            $menuUrl = $data['fk_web_menu_url'] ?? null;

            // Jika level berubah
            if ($oldhakAksesId != $newhakAksesId) {
                // Cek apakah menu dengan nama yang sama sudah ada di level tujuan
                $existingMenu = self::where('wm_menu_nama', $menuName)
                    ->where('fk_m_hak_akses', $newhakAksesId)
                    ->where('web_menu_id', '!=', $id)
                    ->where('isDeleted', 0)
                    ->first();

                if ($existingMenu) {
                    return [
                        'success' => false,
                        'message' => 'Menu ini sudah ada untuk level yang dipilih'
                    ];
                }

                // Update level
                $saveData->fk_m_hak_akses = $newhakAksesId;
            }

            // Jika URL berubah
            if ($menuUrl && $menuUrl != ($saveData->WebMenuGlobal->fk_web_menu_url ?? null)) {
                // Cari WebMenuGlobal yang sesuai dengan URL baru
                $webMenuGlobal = WebMenuGlobalModel::where('fk_web_menu_url', $menuUrl)
                    ->where('isDeleted', 0)
                    ->first();

                if (!$webMenuGlobal) {
                    // Buat baru jika belum ada
                    $webMenuGlobal = WebMenuGlobalModel::create([
                        'fk_web_menu_url' => $menuUrl,
                        'wmg_nama_default' => $menuName
                    ]);

                    // Set wm_menu_nama ke null karena ini adalah menu baru
                    $menuName = null;
                } else {
                    // Jika nama yang diinputkan sama dengan wmg_nama_default dan URL sama
                    if ($menuName === $webMenuGlobal->wmg_nama_default) {
                        // Set wm_menu_nama ke null
                        $menuName = null;
                    }
                    // Jika URL sama tapi nama berbeda, pertahankan nama yang diinputkan
                }

                // Update referensi ke WebMenuGlobal
                $saveData->fk_web_menu_global = $webMenuGlobal->web_menu_global_id;
            } else if (!$menuUrl) {
                // Fix untuk Masalah 4
                // Jika tidak ada URL, cari atau buat WebMenuGlobal baru untuk nama menu
                $webMenuGlobal = WebMenuGlobalModel::where('wmg_nama_default', $menuName)
                    ->whereNull('fk_web_menu_url')
                    ->where('isDeleted', 0)
                    ->first();

                if (!$webMenuGlobal) {
                    // Buat baru jika belum ada
                    $webMenuGlobal = WebMenuGlobalModel::create([
                        'fk_web_menu_url' => null,
                        'wmg_nama_default' => $menuName
                    ]);
                }

                $saveData->fk_web_menu_global = $webMenuGlobal->web_menu_global_id;
                // Set wm_menu_nama ke null karena menggunakan nama default dari WebMenuGlobal
                $menuName = null;
            }

            // Update data menu
            $saveData->wm_menu_nama = $menuName;

            // Update data menu
            $saveData->wm_menu_nama = $menuName;
            $saveData->wm_status_menu = $data['wm_status_menu'];

            // Fix untuk masalah 1: Pastikan parent_id diupdate dengan benar
            // Jika form mengirimkan parent_id kosong atau "", set sebagai null
            $saveData->wm_parent_id = isset($data['wm_parent_id']) && $data['wm_parent_id'] !== '' ? $data['wm_parent_id'] : null;

            $saveData->save();

            TransactionModel::createData(
                'UPDATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama ?: ($saveData->WebMenuGlobal ? $saveData->WebMenuGlobal->wmg_nama_default : 'Menu')
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Menu berhasil diperbarui',
                'data' => $saveData
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage()
            ];
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            // Dapatkan menu yang akan dihapus
            $menu = self::findOrFail($id);

            // Cek level pengguna yang sedang login
            $level = HakAksesModel::find($menu->fk_m_hak_akses);

            // Jika level SAR dan pengguna bukan SAR, tolak
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat menghapus menu SAR'
                ];
            }

            // Cek apakah menu ini memiliki sub menu yang aktif
            $hasActiveSubMenus = self::where('wm_parent_id', $id)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->exists();

            if ($hasActiveSubMenus) {
                return [
                    'success' => false,
                    'message' => 'Menu utama ini tidak dapat dihapus dikarenakan terdapat sub menu yang masih aktif'
                ];
            }

            // Hapus menu
            $menu->isDeleted = 1;
            $menu->deleted_at = now();
            $menu->save();

            // Atur ulang urutan menu
            self::reorderAfterDelete($menu->wm_parent_id ?? null);

            // Catat transaksi
            TransactionModel::createData(
                'DELETED',
                $id,
                $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : 'Menu')
            );

            DB::commit();

            return [
                'success' => true,
                'message' => 'Menu berhasil dihapus',
                'data' => $menu
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage()
            ];
        }
    }

    public static function validasiData($request)
    {
        $rules = [
            'web_menu.wm_menu_nama' => 'required|string|max:60',
            'web_menu.wm_parent_id' => 'nullable|exists:web_menu,web_menu_id',
            'web_menu.wm_status_menu' => 'required|in:aktif,nonaktif',
            // Ubah validasi URL menjadi opsional
            'web_menu.fk_web_menu_url' => 'nullable|exists:web_menu_url,web_menu_url_id',
            'web_menu.fk_m_hak_akses' => 'required|exists:m_hak_akses,hak_akses_id',
        ];

        $messages = [
            'web_menu.wm_menu_nama.required' => 'Nama menu wajib diisi',
            'web_menu.wm_menu_nama.max' => 'Nama menu maksimal 60 karakter',
            'web_menu.wm_parent_id.exists' => 'Parent menu tidak valid',
            'web_menu.wm_status_menu.required' => 'Status menu wajib diisi',
            'web_menu.wm_status_menu.in' => 'Status menu harus aktif atau nonaktif',
            'web_menu.fk_web_menu_url.exists' => 'URL menu tidak valid',
            'web_menu.fk_m_hak_akses.required' => 'Level menu wajib dipilih',
            'web_menu.fk_m_hak_akses.exists' => 'Level menu tidak valid',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getParentMenusByLevel($hakAksesId, $excludeId = null)
    {
        // Dapatkan menu-menu untuk level tertentu
        $query = self::where('fk_m_hak_akses', $hakAksesId)
            ->whereNull('wm_parent_id')
            ->where('isDeleted', 0)
            ->where('wm_status_menu', 'aktif');

        // Jika ada ID yang perlu dikecualikan (menu yang sedang diupdate)
        if ($excludeId) {
            $query->where('web_menu_id', '!=', $excludeId);
        }

        return $query->get()
            ->map(function ($menu) {
                // Tambahkan nama yang benar untuk ditampilkan
                $displayName = $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu');

                // Tambahkan properti untuk tampilan
                $menu->display_name = $displayName;

                return $menu;
            });
    }

    public static function getMenusWithChildren()
    {
        return self::with(['children' => function ($query) {
            $query->orderBy('wm_urutan_menu');
        }])
            ->whereNull('wm_parent_id')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();
    }

    public static function getEditData($id)
    {
        try {
            // Dapatkan menu dengan relasi yang diperlukan
            $menu = self::with(['WebMenuGlobal.WebMenuUrl'])->findOrFail($id);

            // Dapatkan parent menu untuk level tersebut
            $parentMenus = self::getParentMenusByLevel($menu->fk_m_hak_akses);

            $result = [
                'success' => true,
                'menu' => [
                    'web_menu_id' => $menu->web_menu_id,
                    'wm_menu_nama' => $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : ''),
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'fk_web_menu_url' => $menu->WebMenuGlobal ? $menu->WebMenuGlobal->fk_web_menu_url : null,
                    'fk_m_hak_akses' => $menu->fk_m_hak_akses,
                    'hak_akses_kode' => $menu->Level ? $menu->Level->hak_akses_kode : null
                ],
                'parentMenus' => $parentMenus
            ];

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error mengambil data menu: ' . $e->getMessage()
            ];
        }
    }

    public static function getDetailData($id)
    {
        try {
            // Dapatkan menu dengan semua relasi yang diperlukan
            $menu = self::with([
                'WebMenuGlobal.WebMenuUrl',
                'parentMenu',
                'Level'
            ])->findOrFail($id);

            $result = [
                'success' => true,
                'menu' => [
                    'wm_menu_nama' => $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : ''),
                    'wm_menu_url' => $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : null,
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'jenis_menu_nama' => $menu->Level ? $menu->Level->hak_akses_nama : 'Tidak terdefinisi',
                    'hak_akses_kode' => $menu->Level ? $menu->Level->hak_akses_kode : '',
                    'parent_menu_nama' => $menu->parentMenu ? ($menu->parentMenu->wm_menu_nama ?: ($menu->parentMenu->WebMenuGlobal ? $menu->parentMenu->WebMenuGlobal->wmg_nama_default : null)) : null,
                    'created_by' => $menu->created_by,
                    'created_at' => $menu->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $menu->updated_by,
                    'updated_at' => $menu->updated_at ? $menu->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in detail_menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail menu: ' . $e->getMessage()
            ];
        }
    }

    public static function reorderMenus($data)
    {
        try {
            DB::beginTransaction();

            // Kumpulkan semua ID menu untuk memudahkan pengecekan
            $menuIds = [];
            foreach ($data as $item) {
                $menuIds[] = $item['id'];

                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $menuIds[] = $child['id'];
                    }
                }
            }

            // Ambil informasi menu asli dari database
            $originalMenus = self::whereIn('web_menu_id', $menuIds)->get()->keyBy('web_menu_id');

            // Untuk memeriksa duplikasi nama menu dalam satu level
            $menuNamesByLevel = [];

            // Cek apakah ada menu SAR yang dimodifikasi oleh non-SAR
            $userhakAksesKode = Auth::user()->level->hak_akses_kode;
            $hasSARMenuModification = false;

            // Map dari menu ID ke level kode untuk validasi
            $menuLevelMap = [];
            foreach ($originalMenus as $menu) {
                if ($menu->Level) {
                    $menuLevelMap[$menu->web_menu_id] = $menu->Level->hak_akses_kode;
                }
            }

            // Cek perubahan pada menu SAR
            foreach ($data as $item) {
                $menuId = $item['id'];
                $originalLevel = $menuLevelMap[$menuId] ?? null;

                // Jika menu adalah SAR tapi user bukan SAR, tandai ada modifikasi menu SAR
                if ($originalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                    if (isset($item['parent_id']) && $item['parent_id'] != null) {
                        $hasSARMenuModification = true;
                        break;
                    }
                }

                // Cek juga submenu
                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $childId = $child['id'];
                        $childOriginalLevel = $menuLevelMap[$childId] ?? null;

                        // Jika child menu adalah SAR tapi user bukan SAR, tandai ada modifikasi
                        if ($childOriginalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                            $hasSARMenuModification = true;
                            break;
                        }

                        // Jika parent bukan SAR tapi child SAR, ini juga modifikasi tidak valid
                        if ($originalLevel !== 'SAR' && $childOriginalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                            $hasSARMenuModification = true;
                            break;
                        }
                    }
                    if ($hasSARMenuModification) break;
                }
            }

            // Jika ada modifikasi menu SAR oleh non-SAR, tolak request
            if ($hasSARMenuModification) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR'
                ];
            }

            // Mendapatkan mapping level kode ke ID
            $levelMapping = [];
            $levels = HakAksesModel::where('isDeleted', 0)->get();
            foreach ($levels as $level) {
                $levelMapping[$level->hak_akses_kode] = $level->hak_akses_id;
            }

            // Proses reordering dan pemindahan menu
            foreach ($data as $position => $item) {
                $menu = $originalMenus[$item['id']] ?? null;

                if (!$menu) continue;

                // Menentukan parent
                $parentId = $item['parent_id'] ?? null;

                // Data yang akan diupdate
                $updateData = [
                    'wm_parent_id' => $parentId,
                    'wm_urutan_menu' => $position + 1,
                ];

                // Update hak akses jika menu dipindahkan antar level
                if (isset($item['level']) && $parentId === null) {
                    // Hanya update level jika ini adalah menu utama (tanpa parent)
                    $levelId = $levelMapping[$item['level']] ?? null;
                    if ($levelId) {
                        $updateData['fk_m_hak_akses'] = $levelId;
                    }
                } elseif ($parentId) {
                    // Jika ini submenu, ambil level dari parent
                    $parentMenu = $originalMenus[$parentId] ?? null;
                    if ($parentMenu) {
                        $updateData['fk_m_hak_akses'] = $parentMenu->fk_m_hak_akses;
                    }
                }

                // Update menu (tanpa mengubah fk_web_menu_global)
                $menu->update($updateData);

                // Memperbarui submenu
                if (isset($item['children'])) {
                    foreach ($item['children'] as $childPosition => $child) {
                        $childMenu = $originalMenus[$child['id']] ?? null;
                        if ($childMenu) {
                            $childUpdateData = [
                                'wm_parent_id' => $item['id'],
                                'wm_urutan_menu' => $childPosition + 1
                            ];

                            // Set level child sama dengan parent
                            $childUpdateData['fk_m_hak_akses'] = $menu->fk_m_hak_akses;

                            // Update child menu (tanpa mengubah fk_web_menu_global)
                            $childMenu->update($childUpdateData);
                        }
                    }
                }
            }

            $result = [
                'success' => true,
                'message' => 'Urutan menu berhasil diperbarui'
            ];
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengatur ulang urutan menu: ' . $e->getMessage()
            ];
        }
    }

    private static function reorderAfterDelete($parentId)
    {
        $menus = self::where('wm_parent_id', $parentId)
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        foreach ($menus as $index => $menu) {
            $menu->update([
                'wm_urutan_menu' => $index + 1
            ]);
        }
    }

    public static function getDynamicMenuUrl($menuName, $appKey = 'app ppid')
    {
        // Mencari URL secara langsung berdasarkan nama menu dan app_key
        $menuUrl = WebMenuUrlModel::whereHas('Application', function ($query) use ($appKey) {
            $query->where('app_key', $appKey);
        })
            ->where('wmu_nama', $menuName)
            ->first();

        // Jika URL ditemukan, kembalikan nama URL
        if ($menuUrl) {
            return $menuUrl->wmu_nama;
        }

        // Jika URL tidak ditemukan, kembalikan nama menu sebagai fallback
        return $menuName;
    }

    public static function getMenusByLevelWithPermissions($hakAksesKode, $userId)
    {
        // Dapatkan hak_akses_id dari hak_akses_kode
        $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)->first();
        if (!$level) return collect([]);

        $hakAksesId = $level->hak_akses_id;

        // Cek apakah user memiliki hak akses dengan level ini
        $hasLevel = DB::table('set_user_hak_akses')
            ->where('fk_m_user', $userId)
            ->where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->exists();

        if (!$hasLevel && $hakAksesKode !== 'SAR') {
            return collect([]);
        }

        // Ambil menu berdasarkan level
        $menus = self::where('fk_m_hak_akses', $hakAksesId)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereNull('wm_parent_id')
            ->with(['children' => function ($query) use ($hakAksesId) {
                $query->where('fk_m_hak_akses', $hakAksesId)
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->orderBy('wm_urutan_menu');
            }, 'WebMenuGlobal.WebMenuUrl', 'Level'])
            ->orderBy('wm_urutan_menu')
            ->get();

        // Filter menu berdasarkan hak akses
        $filteredMenus = $menus->filter(function ($menu) use ($userId, $hakAksesKode) {
            // Untuk menu utama (parent)
            if (!$menu->wm_parent_id) {
                // Jika tidak ada submenu, cek hak akses langsung
                if ($menu->children->isEmpty()) {
                    return SetHakAksesModel::cekHakAksesMenu($userId, $menu->WebMenuUrl->wmu_nama ?? '');
                }

                // Jika ada submenu, cek apakah salah satu submenu punya hak akses
                $hasAccessibleChildren = $menu->children->contains(function ($child) use ($userId) {
                    return SetHakAksesModel::cekHakAksesMenu($userId, $child->WebMenuUrl->wmu_nama ?? '');
                });

                return $hasAccessibleChildren;
            }

            return false;
        });

        return $filteredMenus;
    }

    // Method untuk mendapatkan notifikasi
    public static function getNotifikasiCount($hakAksesKode)
    {
        switch ($hakAksesKode) {
            case 'ADM':
                return NotifAdminModel::where('sudah_dibaca_notif_admin', null)->count();
            case 'VFR':
                return NotifVerifikatorModel::where('sudah_dibaca_notif_verif', null)->count();
            case 'MPU':
                // Sesuaikan dengan model notifikasi MPU jika ada
                return 0;
            default:
                return 0;
        }
    }

    public function getDisplayName()
    {
        // Gunakan wm_menu_nama jika ada, jika tidak gunakan nama default dari WebMenuGlobal
        return $this->wm_menu_nama ?: ($this->WebMenuGlobal ? $this->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu');
    }
}
