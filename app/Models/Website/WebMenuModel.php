<?php

namespace App\Models\Website;

use App\Models\BaseModel;
use App\Models\Log\TransactionModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Website\WebKontenModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebMenuModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_menu';
    protected $primaryKey = 'web_menu_id';

    protected $fillable = [
        'wm_parent_id',
        'wm_urutan_menu',
        'wm_menu_nama',
        'wm_menu_url',
        'wm_status_menu'
    ];

    // Existing relationships
    public function parentMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        $arr_data =  self::query()
            ->select([
                'web_menu_id',
                'wm_parent_id',
                'wm_menu_url',
                'wm_menu_nama',
                'wm_urutan_menu'
            ])
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_menu_url' => $menu->wm_menu_url,
                    'wm_menu_nama' => $menu->wm_menu_nama,
                    'wm_urutan_menu' => $menu->wm_urutan_menu
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
                'wm_menu_url',
                'wm_menu_nama',
                'wm_urutan_menu'
            ])
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereIn('wm_menu_nama', ['Berita', 'Pengumuman'])
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_menu_url' => $menu->wm_menu_url,
                    'wm_menu_nama' => $menu->wm_menu_nama,
                    'wm_urutan_menu' => $menu->wm_urutan_menu
                ];
            })->toArray();
        return $arr_data;
    }

    public static function mengecekKetersediaanMenu($menuName, $excludeId = null)
    {
        $query = self::where('wm_menu_nama', $menuName)
            ->where('isDeleted', 0);

        if ($excludeId) {
            $query->where('web_menu_id', '!=', $excludeId);
        }

        $menuAktif = clone $query;
        $menuAktif = $menuAktif->where('wm_status_menu', 'aktif')->first();

        if ($menuAktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada dan berstatus aktif'
            ];
        }

        $menuNonaktif = clone $query;
        $menuNonaktif = $menuNonaktif->where('wm_status_menu', 'nonaktif')->first();

        if ($menuNonaktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada, tetapi saat ini berstatus nonaktif'
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

            // Cek apakah nama menu sudah ada dan belum di-soft delete
            $menuCheck = self::mengecekKetersediaanMenu($data['wm_menu_nama']);

            // Jika menu dengan nama sama ditemukan dan belum di-soft delete
            if ($menuCheck['exists']) {
                return [
                    'success' => false,
                    'message' => $menuCheck['message']
                ];
            }

            // Menu belum ada atau sudah di-soft delete, jadi bisa lanjutkan pembuatan menu baru
            $orderNumber = self::where('wm_parent_id', $data['wm_parent_id'])
                ->where('isDeleted', 0)
                ->count() + 1;

            $data['wm_menu_url'] = Str::slug($data['wm_menu_nama']);
            $data['wm_urutan_menu'] = $orderNumber;

            $saveData = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama
            );

            $result = [
                'success' => true,
                'message' => 'Menu berhasil dibuat',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
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
        try {
            self::validasiData($request);

            $saveData = self::findOrFail($id);
            $data = $request->web_menu;

            // Cek apakah nama menu sudah ada dan belum di-soft delete
            $menuCheck = self::mengecekKetersediaanMenu($data['wm_menu_nama'], $id);

            // Jika menu dengan nama sama ditemukan dan belum di-soft delete
            if ($menuCheck['exists']) {
                return [
                    'success' => false,
                    'message' => $menuCheck['message']
                ];
            }

            if ($data['wm_parent_id']) {
                $isChild = self::where('wm_parent_id', $id)
                    ->where('web_menu_id', $data['wm_parent_id'])
                    ->exists();

                if ($isChild) {
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat mengatur menu anak sebagai parent'
                    ];
                }
            }

            DB::beginTransaction();

            $data['wm_menu_url'] = Str::slug($data['wm_menu_nama']);
            $saveData->update($data);

            TransactionModel::createData(
                'UPDATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama
            );

            $result = [
                'success' => true,
                'message' => 'Menu berhasil diperbaharui',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
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
            $saveData = self::findOrFail($id);

            if ($saveData->children()->where('isDeleted', 0)->exists()) {
                return [
                    'success' => false, // Perbaikan: mengganti 's' menjadi 'success'
                    'message' => 'Tidak dapat menghapus menu yang memiliki submenu aktif. Silahkan hapus submenu terlebih dahulu'
                ];
            }

            DB::beginTransaction();

            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();
            self::reorderAfterDelete($saveData->wm_parent_id);

            // Mencatat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama
            );

            $result = [
                'success' => true,
                'message' => 'Menu berhasil dihapus',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
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
        ];

        $messages = [
            'web_menu.wm_menu_nama.required' => 'Nama menu wajib diisi',
            'web_menu.wm_menu_nama.max' => 'Nama menu maksimal 60 karakter',
            'web_menu.wm_parent_id.exists' => 'Parent menu tidak valid',
            'web_menu.wm_status_menu.required' => 'Status menu wajib diisi',
            'web_menu.wm_status_menu.in' => 'Status menu harus aktif atau nonaktif',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
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
            $menu = self::findOrFail($id);
            $parentMenus = self::whereNull('wm_parent_id')
                ->where('web_menu_id', '!=', $id)
                ->where('isDeleted', 0)
                ->whereNotIn('web_menu_id', function ($query) use ($id) {
                    $query->select('web_menu_id')
                        ->from('web_menu')
                        ->where('wm_parent_id', $id);
                })
                ->get();

            $result = [
                'success' => true,
                'menu' => $menu,
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
            $menu = self::with('parentMenu')->find($id);

            if (!$menu) {
                return [
                    'success' => false,
                    'message' => 'Menu tidak ditemukan'
                ];
            }

            $result = [
                'success' => true,
                'menu' => [
                    'wm_menu_nama' => $menu->wm_menu_nama,
                    'wm_menu_url' => $menu->wm_menu_url,
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'parent_menu_nama' => $menu->parentMenu ? $menu->parentMenu->wm_menu_nama : null,
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
            foreach ($data as $position => $item) {
                $menu = self::find($item['id']);
                if ($menu) {
                    $menu->update([
                        'wm_parent_id' => $item['parent_id'] ?? null,
                        'wm_urutan_menu' => $position + 1,
                    ]);

                    if (isset($item['children'])) {
                        foreach ($item['children'] as $childPosition => $child) {
                            $childMenu = self::find($child['id']);
                            if ($childMenu) {
                                $childMenu->update([
                                    'wm_parent_id' => $item['id'],
                                    'wm_urutan_menu' => $childPosition + 1,
                                ]);
                            }
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
}