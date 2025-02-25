<?php

namespace App\Models\Website;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebKontenImagesModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_konten_images';
    protected $primaryKey = 'konten_images_id';

    protected $fillable = [
        'fk_web_konten',
        'wki_image_webkonten',
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public function konten()
    {
        return $this->belongsTo(WebKontenModel::class, 'fk_web_konten', 'web_konten_id');
    }

    public static function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $image = self::findOrFail($id);
            
            // Delete file if exists
            $filePath = public_path($image->wki_image_webkonten);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $image->deleted_by = session('alias');
            $image->isDeleted = 1;
            $image->deleted_at = now();
            $image->save();
            
            TransactionModel::createData();
            
            DB::commit();
            return [
                'status' => true,
                'message' => 'Gambar berhasil dihapus'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting image: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus gambar'
            ];
        }
    }

    public static function createImage($kontenId, $imagePath)
    {
        DB::beginTransaction();
        try {
            $image = self::create([
                'fk_web_konten' => $kontenId,
                'wki_image_webkonten' => $imagePath,
                'created_by' => session('alias')
            ]);
            
            TransactionModel::createData();
            
            DB::commit();
            return [
                'status' => true,
                'message' => 'Gambar berhasil ditambahkan',
                'data' => $image
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating image: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat menambahkan gambar'
            ];
        }
    }

    public static function getImagesByKonten($kontenId)
    {
        try {
            $images = self::where('fk_web_konten', $kontenId)
                ->where('isDeleted', 0)
                ->get();
                
            return [
                'status' => true,
                'images' => $images->map(function($image) {
                    return [
                        'konten_images_id' => $image->konten_images_id,
                        'wki_image_webkonten' => $image->wki_image_webkonten,
                        'url' => asset($image->wki_image_webkonten)
                    ];
                })
            ];
        } catch (\Exception $e) {
            Log::error('Error getting images by konten: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil gambar'
            ];
        }
    }
}