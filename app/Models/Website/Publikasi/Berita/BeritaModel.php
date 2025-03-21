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
        'status_berita',
    ];

    public function BeritaDinamis()
    {
        return $this->belongsTo(BeritaDinamisModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
    }

    public function uploadBerita()
    {
        return $this->hasMany(UploadBeritaModel::class, 'fk_t_berita', 'berita_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Fungsi untuk mengambil semua data dengan pagination
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0)
            ->with('BeritaDinamis');

        // Add search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('berita_judul', 'like', "%{$search}%")
                  ->orWhere('berita_deskripsi', 'like', "%{$search}%")
                  ->orWhere('berita_link', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    // Fungsi untuk membuat data baru
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            self::validasiData($request);

            // Generate slug
            $slug = Str::slug($request->t_berita['berita_judul'] ?? $request->t_berita['berita_link']);

            // Persiapan data berita
            $data = $request->t_berita;
            $data['berita_slug'] = $slug;

            // Upload thumbnail jika ada
            if ($request->hasFile('berita_thumbnail')) {
                $thumbnailPath = $request->file('berita_thumbnail')->store('public/thumbnails');
                $data['berita_thumbnail'] = str_replace('public/', '', $thumbnailPath);
            }

            // Buat record berita
            $saveData = self::create($data);

            // Simpan upload berita jika ada
            if ($request->has('upload_berita')) {
                foreach ($request->upload_berita as $upload) {
                    UploadBeritaModel::create([
                        'fk_t_berita' => $saveData->berita_id,
                        'ub_type' => $upload['type'],
                        'ub_value' => $upload['value']
                    ]);
                }
            }

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->berita_id,
                $saveData->berita_judul ?? $saveData->berita_link
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Berita');
        }
    }

    // Fungsi untuk mengupdate data
    public static function updateData($request, $id)
    {
        try {
            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($request->t_berita['berita_judul'] ?? $request->t_berita['berita_link']);

            // Persiapan data berita
            $data = $request->t_berita;
            $data['berita_slug'] = $slug;

            // Upload thumbnail jika ada
            if ($request->hasFile('berita_thumbnail')) {
                // Hapus thumbnail lama jika ada
                if ($saveData->berita_thumbnail) {
                    Storage::delete('public/' . $saveData->berita_thumbnail);
                }

                $thumbnailPath = $request->file('berita_thumbnail')->store('public/thumbnails');
                $data['berita_thumbnail'] = str_replace('public/', '', $thumbnailPath);
            }

            // Update record
            $saveData->update($data);

            // Hapus upload berita yang ada
            UploadBeritaModel::where('fk_t_berita', $id)->delete();

            // Simpan upload berita baru jika ada
            if ($request->has('upload_berita')) {
                foreach ($request->upload_berita as $upload) {
                    UploadBeritaModel::create([
                        'fk_t_berita' => $saveData->berita_id,
                        'ub_type' => $upload['type'],
                        'ub_value' => $upload['value']
                    ]);
                }
            }

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->berita_id,
                $saveData->berita_judul ?? $saveData->berita_link
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Berita');
        }
    }

    // Fungsi untuk menghapus data
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Hapus thumbnail jika ada
            if ($saveData->berita_thumbnail) {
                Storage::delete('public/' . $saveData->berita_thumbnail);
            }

            // Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();

            // Soft delete upload berita terkait
            UploadBeritaModel::where('fk_t_berita', $id)->update([
                'isDeleted' => 1,
                'deleted_at' => now()
            ]);

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->berita_id,
                $saveData->berita_judul ?? $saveData->berita_link
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Berita');
        }
    }

    // Fungsi untuk detail data
    public static function detailData($id)
    {
        try {
            $berita = self::with(['BeritaDinamis', 'uploadBerita'])->findOrFail($id);
            return $berita;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_berita.fk_m_berita_dinamis' => 'required|exists:m_berita_dinamis,berita_dinamis_id',
            't_berita.berita_type' => 'required|in:file,link',
            't_berita.status_berita' => 'required|in:aktif,nonaktif'
        ];

        // Validasi berbeda berdasarkan tipe berita
        if ($request->input('t_berita.berita_type') === 'file') {
            $rules += [
                't_berita.berita_judul' => [
                    'required',
                    'max:255',
                    function ($attribute, $value, $fail) use ($id) {
                        // Cek duplikasi judul
                        $query = self::where('berita_judul', $value)
                            ->where('isDeleted', 0)
                            ->where('berita_type', 'file');

                        if ($id) {
                            $query->where('berita_id', '!=', $id);
                        }

                        if ($query->exists()) {
                            $fail('Judul berita sudah digunakan');
                        }
                    }
                ],
                'berita_thumbnail' => 'nullable|image|max:2560',  // 2.5 MB
                't_berita.berita_thumbnail_deskripsi' => 'nullable|max:255',
                't_berita.berita_deskripsi' => 'required'
            ];
        } else {
            // Validasi untuk tipe link
            $rules += [
                't_berita.berita_link' => [
                    'required', 
                    'url',
                    function ($attribute, $value, $fail) use ($id) {
                        // Cek duplikasi link
                        $query = self::where('berita_link', $value)
                            ->where('isDeleted', 0)
                            ->where('berita_type', 'link');

                        if ($id) {
                            $query->where('berita_id', '!=', $id);
                        }

                        if ($query->exists()) {
                            $fail('Link berita sudah digunakan');
                        }
                    }
                ]
            ];
        }

        $messages = [
            't_berita.fk_m_berita_dinamis.required' => 'Kategori berita wajib dipilih',
            't_berita.fk_m_berita_dinamis.exists' => 'Kategori berita tidak valid',
            't_berita.berita_type.required' => 'Tipe berita wajib dipilih',
            't_berita.berita_type.in' => 'Tipe berita hanya boleh file atau link',
            't_berita.status_berita.required' => 'Status berita wajib dipilih',
            't_berita.status_berita.in' => 'Status berita hanya boleh aktif atau nonaktif',
            't_berita.berita_judul.required' => 'Judul berita wajib diisi',
            't_berita.berita_judul.max' => 'Judul berita maksimal 255 karakter',
            'berita_thumbnail.image' => 'Thumbnail harus berupa gambar',
            'berita_thumbnail.max' => 'Ukuran thumbnail maksimal 2.5 MB',
            't_berita.berita_thumbnail_deskripsi.max' => 'Deskripsi thumbnail maksimal 255 karakter',
            't_berita.berita_deskripsi.required' => 'Konten berita wajib diisi',
            't_berita.berita_link.required' => 'Link berita wajib diisi',
            't_berita.berita_link.url' => 'Format link tidak valid'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    // Method untuk upload gambar
    public static function uploadImage($file)
    {
        if (!$file) {
            return null;
        }
        
        $fileName = 'berita/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        
        return asset('storage/' . $fileName);
    }

    // Method untuk menghapus gambar
    public static function removeImage($fileName)
    {
        if (!$fileName) {
            return false;
        }
        
        $pathInfo = parse_url($fileName);
        $path = $pathInfo['path'] ?? '';
        $storagePath = str_replace('/storage/', '', $path);
        
        if (!empty($storagePath)) {
            Storage::delete('public/' . $storagePath);
            return true;
        }
        
        return false;
    }
}