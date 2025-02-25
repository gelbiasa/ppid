<?php

namespace App\Models\Website;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Log\TransactionModel;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebKontenModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_konten';
    protected $primaryKey = 'web_konten_id';

    protected $fillable = [
        'fk_web_menu',
        'wk_judul_konten',
        'wk_deskripsi_konten',
        'wk_status_konten'
    ];

    // Relationships
    public function menu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function images()
    {
        return $this->hasMany(WebKontenImagesModel::class, 'fk_web_konten', 'web_konten_id')
            ->where('isDeleted', 0);
    }

    public static function createData($request)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);
            
            $data = [
                'fk_web_menu' => $request->fk_web_menu,
                'wk_judul_konten' => $request->wk_judul_konten,
                'wk_deskripsi_konten' => $request->wk_deskripsi_konten,
                'wk_status_konten' => $request->wk_status_konten,
            ];
            
            $konten = self::create($data);
            
            // Handle images if present
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/konten'), $imageName);
                    
                    WebKontenImagesModel::create([
                        'fk_web_konten' => $konten->web_konten_id,
                        'wki_image_webkonten' => 'uploads/konten/' . $imageName,
                    ]);
                }
            }
            
            TransactionModel::createData(
                
                'CREATED', 
                $konten->web_konten_id,
                $konten->wk_judul_konten
            );
            
            DB::commit();
            return [
                'status' => true,
                'message' => 'Konten berhasil dibuat',
                'data' => $konten
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating konten: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat konten'
            ];
        }
    }

    public static function updateData($request, $id)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);

            $konten = self::findOrFail($id);
            
            $konten->update([
                'fk_web_menu' => $request->fk_web_menu,
                'wk_judul_konten' => $request->wk_judul_konten,
                'wk_deskripsi_konten' => $request->wk_deskripsi_konten,
                'wk_status_konten' => $request->wk_status_konten,
            ]);

            // Handle images if present
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/konten'), $imageName);
                    
                    WebKontenImagesModel::create([
                        'fk_web_konten' => $konten->web_konten_id,
                        'wki_image_webkonten' => 'uploads/konten/' . $imageName,
                    ]);
                }
            }
            
            // Handle deleted images
            if ($request->has('deleted_images') && is_array($request->deleted_images)) {
                foreach ($request->deleted_images as $imageId) {
                    WebKontenImagesModel::deleteData($imageId);
                }
            }

            TransactionModel::createData(
                
                'UPDATED', 
                $konten->web_konten_id,
                $konten->wk_judul_konten
            );

            DB::commit();
            return [
                'status' => true,
                'message' => 'Konten berhasil diperbarui',
                'data' => $konten
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating konten: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui konten'
            ];
        }
    }

    public static function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $konten = self::findOrFail($id);
            
            // Soft delete all related images
            WebKontenImagesModel::where('fk_web_konten', $id)
                ->update([
                    'isDeleted' => 1,
                    'deleted_at' => now()
                ]);
            
            $konten->isDeleted = 1;
            $konten->deleted_at = now();
            $konten->save();
            
            TransactionModel::createData(
                'DELETED', 
                $konten->web_konten_id,
                $konten->wk_judul_konten
            );

            DB::commit();
            return [
                'status' => true,
                'message' => 'Konten berhasil dihapus'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting konten: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus konten'
            ];
        }
    }

    public static function validasiData($request)
    {
        $rules = [
            'fk_web_menu' => 'required|exists:web_menu,web_menu_id',
            'wk_judul_konten' => 'required|string|max:200',
            'wk_deskripsi_konten' => 'required',
            'wk_status_konten' => 'required|in:aktif,nonaktif',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $messages = [
            'fk_web_menu.required' => 'Menu wajib dipilih',
            'fk_web_menu.exists' => 'Menu tidak valid',
            'wk_judul_konten.required' => 'Judul konten wajib diisi',
            'wk_judul_konten.max' => 'Judul konten maksimal 200 karakter',
            'wk_deskripsi_konten.required' => 'Deskripsi konten wajib diisi',
            'wk_status_konten.required' => 'Status konten wajib dipilih',
            'wk_status_konten.in' => 'Status konten harus aktif atau nonaktif',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'images.*.max' => 'Ukuran gambar maksimal 2MB'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getDetailData($id)
    {
        try {
            $konten = self::with(['menu', 'images'])
                ->where('web_konten_id', $id)
                ->first();

            if (!$konten) {
                return [
                    'status' => false,
                    'message' => 'Konten tidak ditemukan'
                ];
            }

            return [
                'status' => true,
                'konten' => [
                    'web_konten_id' => $konten->web_konten_id,
                    'fk_web_menu' => $konten->fk_web_menu,
                    'menu_nama' => $konten->menu ? $konten->menu->wm_menu_nama : null,
                    'wk_judul_konten' => $konten->wk_judul_konten,
                    'wk_deskripsi_konten' => $konten->wk_deskripsi_konten,
                    'wk_status_konten' => $konten->wk_status_konten,
                    'images' => $konten->images->map(function($image) {
                        return [
                            'konten_images_id' => $image->konten_images_id,
                            'wki_image_webkonten' => $image->wki_image_webkonten,
                            'url' => asset($image->wki_image_webkonten)
                        ];
                    }),
                    'created_by' => $konten->created_by,
                    'created_at' => $konten->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $konten->updated_by,
                    'updated_at' => $konten->updated_at ? $konten->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in detail_konten: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail konten'
            ];
        }
    }

    public static function getKontenByMenu($menuId)
    {
        try {
            $konten = self::with('images')
                ->where('fk_web_menu', $menuId)
                ->where('isDeleted', 0)
                ->where('wk_status_konten', 'aktif')
                ->first();

            if (!$konten) {
                return [
                    'status' => false,
                    'message' => 'Konten tidak ditemukan'
                ];
            }

            return [
                'status' => true,
                'konten' => $konten
            ];
        } catch (\Exception $e) {
            Log::error('Error getting konten by menu: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil konten'
            ];
        }
    }
}