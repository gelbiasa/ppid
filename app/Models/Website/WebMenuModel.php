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

    public static function selectData(){
        $arr_data =  self::query()
            ->select([
               'web_menu_id',
               'wm_menu_nama',
               'wm_menu_url',
               'wm_parent_id',
               'wm_urutan_menu'
           ])
           ->where('wm_status_menu', 'aktif')
           ->where('isDeleted', 0)
           ->orderBy('wm_urutan_menu')
           ->get()
           ->map(function ($menu) {
               return [
                   'id' => $menu->web_menu_id,
                   'wm_menu_nama' => $menu->wm_menu_nama,
                   'wm_menu_url' => $menu->wm_menu_url,
                   'wm_parent_id' => $menu->wm_parent_id,
                   'wm_urutan_menu' => $menu->wm_urutan_menu
               ];
           })->toArray();
           return $arr_data;
}

    public static function createData($request)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);
    
            $orderNumber = self::where('wm_parent_id', $request->wm_parent_id)
                ->where('isDeleted', 0)
                ->count() + 1;
    
            $menu = self::create([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_parent_id' => $request->wm_parent_id,
                'wm_urutan_menu' => $orderNumber,
                'wm_status_menu' => $request->wm_status_menu,
            ]);
    
            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED', 
                $menu->web_menu_id,
                $menu->wm_menu_nama
            );
    
            $result = [
                'status' => true,
                'message' => 'Menu berhasil dibuat',
                'data' => $menu
            ];
    
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat menu'
            ];
        }
    }

    public static function updateData($request, $id)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);

            $menu = self::findOrFail($id);

            if ($request->wm_parent_id) {
                $isChild = self::where('wm_parent_id', $id)
                    ->where('web_menu_id', $request->wm_parent_id)
                    ->exists();

                if ($isChild) {
                    return [
                        'status' => false,
                        'message' => 'Tidak dapat mengatur menu anak sebagai parent'
                    ];
                }
            }

            $menu->update([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_parent_id' => $request->wm_parent_id,
                'wm_status_menu' => $request->wm_status_menu,
            ]);

            // Mencatat log transaksi
            TransactionModel::createData(
                'UPDATED', 
                $menu->web_menu_id,
                $menu->wm_menu_nama
            );

            $result = [
                'status' => true,
                'message' => 'Menu berhasil diperbaharui',
                'data' => $menu
            ];

            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui menu'
            ];
        }
    }

    public static function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $menu = self::findOrFail($id);

            if ($menu->children()->where('isDeleted', 0)->exists()) {
                return [
                    'status' => false,
                    'message' => 'Tidak dapat menghapus menu yang memiliki submenu aktif,Silahkan Hapus Submenu Terlebih Dahulu'
                ];
            }
            $menu->isDeleted = 1;
            $menu->deleted_at = now();
            $menu->save();
            self::reorderAfterDelete($menu->wm_parent_id);

            // Mencatat log transaksi
            TransactionModel::createData(
                'DELETED', 
                $menu->web_menu_id,
                $menu->wm_menu_nama
            );

            $result = [
                'status' => true,
                'message' => 'Menu berhasil dihapus',
                'data' => $menu
            ];
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus menu'
            ];
        }
    }

    public static function validasiData($request)
    {
        $rules = [
            'wm_menu_nama' => 'required|string|max:60',
            'wm_parent_id' => 'nullable|exists:web_menu,web_menu_id',
            'wm_status_menu' => 'required|in:aktif,nonaktif',
        ];

        $messages = [
            'wm_menu_nama.required' => 'Nama menu wajib diisi',
            'wm_menu_nama.max' => 'Nama menu maksimal 60 karakter',
            'wm_parent_id.exists' => 'Parent menu tidak valid',
            'wm_status_menu.required' => 'Status menu wajib diisi',
            'wm_status_menu.in' => 'Status menu harus aktif atau nonaktif',
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

            return [
                'status' => true,
                'menu' => $menu,
                'parentMenus' => $parentMenus
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching menu for edit: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Error mengambil data menu'
            ];
        }
    }

    public static function getDetailData($id)
    {
        try {
            $menu = self::with('parentMenu')->find($id);

            if (!$menu) {
                return [
                    'status' => false,
                    'message' => 'Menu tidak ditemukan'
                ];
            }

            return [
                'status' => true,
                'menu' => [
                    'wm_menu_nama' => $menu->wm_menu_nama,
                    'wm_menu_url' => $menu->wm_menu_url,
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'parent_menu_nama' => $menu->parentMenu ? $menu->parentMenu->wm_menu_nama : null, // Ubah ini
                    'created_by' => $menu->created_by,
                    'created_at' => $menu->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $menu->updated_by,
                    'updated_at' => $menu->updated_at ? $menu->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in detail_menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail menu'
            ];
        }
    }

    public static function reorderMenus($data)
    {
        DB::beginTransaction();
        try {
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

            DB::commit();
            return [
                'status' => true,
                'message' => 'Urutan menu berhasil diperbarui'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reordering menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengatur ulang urutan menu'
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