<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\TraitsModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BeritaModel extends Model
{
    use TraitsModel;

    protected $table = 't_berita';
    protected $primaryKey = 'berita_id';
    protected $fillable = [
        'fk_m_berita_dinamis',
        'berita_judul',
        'berita_slug',
        'berita_thumbnail',
        'berita_thumbnail_deskripsi',
        'berita_deskripsi',
        'status_berita'
    ];

    // Relationship with BeritaDinamis
    public function BeritaDinamis()
    {
        return $this->belongsTo(BeritaDinamisModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
    }

    // Relationship with UploadBerita
    public function uploadBerita()
    {
        return $this->hasMany(UploadBeritaModel::class, 'fk_t_berita', 'berita_id');
    }

    // Select data with search and pagination
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0)
            ->with('BeritaDinamis');

        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('berita_judul', 'like', "%{$search}%")
                  ->orWhere('berita_deskripsi', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }
    // Modify createData method to handle image cleanup
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validate input
            self::validasiData($request);

            // Prepare data
            $data = $request->t_berita;
            
            // Generate unique slug - limit to 100 chars per database constraint
            $data['berita_slug'] = self::generateUniqueSlug($data['berita_judul']);

            // Handle thumbnail upload
            if (request()->hasFile('berita_thumbnail')) {
                $thumbnailPath = request()->file('berita_thumbnail')->store('public/thumbnails');
                // Save path with max 100 chars
                $storagePath = str_replace('public/', '', $thumbnailPath);
                $data['berita_thumbnail'] = substr($storagePath, 0, 100);
            }

            // Create berita record
            $berita = self::create($data);

            // Handle image uploads from Summernote and cleanup
            $uploadedImages = self::handleSummernoteImages($berita, $data['berita_deskripsi']);

            // Log transaction
            TransactionModel::createData(
                'CREATED',
                $berita->berita_id,
                $berita->berita_judul
            );

            DB::commit();
            return $berita;
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Cleanup uploaded images if transaction fails
            if (isset($uploadedImages)) {
                foreach ($uploadedImages as $image) {
                    Storage::delete('public/' . $image);
                }
            }
            
            throw $e;
        }
    }

    // Modify updateData method to handle image cleanup
    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            // Validate input
            self::validasiData($request, $id);

            // Find existing record
            $berita = self::findOrFail($id);

            // Prepare data
            $data = $request->t_berita;
            
            // Store old thumbnail and uploaded images for potential cleanup
            $oldThumbnail = $berita->berita_thumbnail;
            $oldUploadedImages = self::getExistingUploadedImages($berita);

            // Generate unique slug if judul changed - respect 100 char limit
            if ($berita->berita_judul !== $data['berita_judul']) {
                $data['berita_slug'] = self::generateUniqueSlug($data['berita_judul']);
            }

            // Handle thumbnail upload
            if (request()->hasFile('berita_thumbnail')) {
                // Delete old thumbnail if exists
                if ($oldThumbnail) {
                    Storage::delete('public/' . $oldThumbnail);
                }

                $thumbnailPath = request()->file('berita_thumbnail')->store('public/thumbnails');
                // Save path with max 100 chars
                $storagePath = str_replace('public/', '', $thumbnailPath);
                $data['berita_thumbnail'] = substr($storagePath, 0, 100);
            }

            // Update berita record
            $berita->update($data);

            // Handle image uploads from Summernote and cleanup old images
            $newUploadedImages = self::handleSummernoteImages($berita, $data['berita_deskripsi']);
            self::cleanupUnusedImages($oldUploadedImages, $newUploadedImages);

            // Log transaction
            TransactionModel::createData(
                'UPDATED',
                $berita->berita_id,
                $berita->berita_judul
            );

            DB::commit();
            return $berita;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Modify deleteData method to handle image cleanup
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            // Find existing record
            $berita = self::findOrFail($id);

            // Store images to be deleted
            $thumbnailToDelete = $berita->berita_thumbnail;
            $uploadedImagesToDelete = self::getExistingUploadedImages($berita);

            // Soft delete
            $berita->isDeleted = 1;
            $berita->deleted_at = now();
            $berita->save();

            // Soft delete terkait uploads
            UploadBeritaModel::where('fk_t_berita', $id)
                ->update([
                    'isDeleted' => 1,
                    'deleted_at' => now()
                ]);

            // Delete physical files
            if ($thumbnailToDelete) {
                Storage::delete('public/' . $thumbnailToDelete);
            }

            foreach ($uploadedImagesToDelete as $image) {
                Storage::delete('public/' . $image);
            }
             // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
             $berita->delete();
            // Log transaction
            TransactionModel::createData(
                'DELETED',
                $berita->berita_id,
                $berita->berita_judul
            );

            DB::commit();
            return self::responFormatSukses($berita, 'Footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus footer');
        }
    }

    private static function generateUniqueSlug($title)
    {
        // Create initial slug and limit to 90 chars to allow for possible suffixes
        $slug = substr(Str::slug($title), 0, 90);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('berita_slug', $slug)->exists()) {
            $suffix = '-' . $count;
            $slug = substr($originalSlug, 0, 90 - strlen($suffix)) . $suffix;
            $count++;
        }

        return $slug;
    }
    // Validation method
    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_berita.fk_m_berita_dinamis' => 'required|exists:m_berita_dinamis,berita_dinamis_id',
            't_berita.berita_judul' => [
                'required',
                'max:150', // varchar(255) in database
                function ($attribute, $value, $fail) use ($id) {
                    $query = self::where('berita_judul', $value)
                        ->where('isDeleted', 0);

                    if ($id) {
                        $query->where('berita_id', '!=', $id);
                    }

                    if ($query->exists()) {
                        $fail('Judul berita sudah digunakan');
                    }
                }
            ],
            't_berita.status_berita' => 'required|in:aktif,nonaktif',
            'berita_thumbnail' => 'nullable|image|max:2560',  // 2.5 MB
            't_berita.berita_thumbnail_deskripsi' => 'nullable|max:255', // varchar(255) in database
            't_berita.berita_deskripsi' => 'required'
        ];

        // Check if slug will exceed maximum length (100 chars)
        if (isset($request->t_berita['berita_judul'])) {
            $potentialSlug = Str::slug($request->t_berita['berita_judul']);
            if (strlen($potentialSlug) > 150) {
                $rules['t_berita.berita_judul'][] = function ($attribute, $value, $fail) {
                    $fail('Judul terlalu panjang, akan menghasilkan slug yang melebihi batas 100 karakter');
                };
            }
        }

        $messages = [
            't_berita.fk_m_berita_dinamis.required' => 'Kategori berita wajib dipilih',
            't_berita.fk_m_berita_dinamis.exists' => 'Kategori berita tidak valid',
            't_berita.berita_judul.required' => 'Judul berita wajib diisi',
            't_berita.berita_judul.max' => 'Judul berita maksimal 150 karakter',
            't_berita.status_berita.required' => 'Status berita wajib dipilih',
            't_berita.status_berita.in' => 'Status berita hanya boleh aktif atau nonaktif',
            'berita_thumbnail.image' => 'Thumbnail harus berupa gambar',
            'berita_thumbnail.max' => 'Ukuran thumbnail maksimal 2.5 MB',
            't_berita.berita_thumbnail_deskripsi.max' => 'Deskripsi thumbnail maksimal 255 karakter',
            't_berita.berita_deskripsi.required' => 'Konten berita wajib diisi'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    // Handle image uploads from Summernote and return list of uploaded images
    private static function handleSummernoteImages($berita, $content)
    {
        $uploadedImages = [];
        $dom = new \DOMDocument();
        @$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            
            // Check if image is base64
            if (strpos($src, 'data:image') === 0) {
                // Decode and save base64 image
                list($type, $data) = explode(';', $src);
                list(, $data) = explode(',', $data);
                
                $imageData = base64_decode($data);
                $fileName = 'berita/' . uniqid() . '.png';
                
                // Save file
                Storage::put('public/' . $fileName, $imageData);
                
                // Update src in content
                $img->setAttribute('src', asset('storage/' . $fileName));
                
                // Save to upload berita
                UploadBeritaModel::create([
                    'fk_t_berita' => $berita->berita_id,
                    'ub_type' => 'file',
                    'ub_value' => $fileName
                ]);

                $uploadedImages[] = $fileName;
            }
        }

        return $uploadedImages;
    }

    // Get existing uploaded images for a berita
    private static function getExistingUploadedImages($berita)
    {
        return $berita->uploadBerita()
            ->where('isDeleted', 0)
            ->pluck('ub_value')
            ->toArray();
    }

    // Cleanup unused images
    private static function cleanupUnusedImages($oldImages, $newImages)
    {
        $imagesToDelete = array_diff($oldImages, $newImages);
        
        foreach ($imagesToDelete as $image) {
            Storage::delete('public/' . $image);
            
            // Remove from UploadBeritaModel
            UploadBeritaModel::where('ub_value', $image)->delete();
        }
    }
}