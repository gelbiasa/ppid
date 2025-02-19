<?php

namespace App\Models\Website;

use App\Models\Website\WebKontenModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebMenuModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_menu';
    protected $primaryKey = 'web_menu_id';

    protected $fillable = [
        'wm_parent_id',
        'wm_urutan_menu',
        'wm_menu_nama',
        'wm_menu_url',
        'wm_status_menu',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    // Existing relationships
    public function submenus()
    {
        return $this->hasMany(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function parentMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function children()
    {
        return $this->hasMany(WebMenuModel::class, 'wm_parent_id', 'web_menu_id')
                    ->orderBy('wm_urutan_menu');
    }
    
    public function parent()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function konten()
    {
        return $this->hasOne(WebKontenModel::class, 'fk_web_menu', 'web_menu_id');
    }

    // New static methods for handling menu operations
    public static function validasiData($request)
    {
        $rules = [
            'wm_menu_nama' => 'required|string|max:60',
            'wm_status_menu' => 'required|in:aktif,nonaktif',
        ];

        $messages = [
            'wm_menu_nama.required' => 'Nama menu wajib diisi',
            'wm_menu_nama.max' => 'Nama menu maksimal 60 karakter',
            'wm_status_menu.required' => 'Status menu wajib diisi',
            'wm_status_menu.in' => 'Status menu harus aktif atau nonaktif',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function createData($request)
    {
        try {
            self::validasiData($request);

            $orderNumber = self::whereNull('wm_parent_id')->count() + 1;

            $menu = self::create([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_parent_id' => null,
                'wm_urutan_menu' => $orderNumber,
                'wm_status_menu' => $request->wm_status_menu,
                'created_by' => session('alias'),
            ]);

            return [
                'success' => true,
                'message' => 'Menu utama berhasil disimpan',
                'data' => $menu
            ];
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            Log::error('Error creating menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    public static function updateData($request, $id)
    {
        try {
            self::validasiData($request);

            $menu = self::findOrFail($id);
            
            $menu->update([
                'wm_menu_nama' => $request->wm_menu_nama,
                'wm_menu_url' => Str::slug($request->wm_menu_nama),
                'wm_status_menu' => $request->wm_status_menu,
                'updated_by' => session('alias'),
            ]);

            return [
                'success' => true,
                'message' => 'Menu utama berhasil diperbarui',
                'data' => $menu
            ];
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            Log::error('Error updating menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    public static function deleteData($id)
    {
        try {
            $menu = self::findOrFail($id);

            // Check for submenu
            if ($menu->submenus()->exists()) {
                return [
                    'success' => false,
                    'message' => 'Menu tidak bisa dihapus karena memiliki submenu di dalamnya'
                ];
            }

            $menu->update([
                'deleted_by' => session('alias'),
                'isDeleted' => 1
            ]);

            $menu->delete();

            return [
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ];
        }
    }
}