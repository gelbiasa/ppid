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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Select data with search and pagination
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->with('BeritaDinamis')
            ->where('isDeleted', 0);

        // Search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('berita_judul', 'like', "%{$search}%")
                    ->orWhere('berita_deskripsi', 'like', "%{$search}%");
            });
        }

        // Gunakan paginateResults dari trait BaseModelFunction
        return self::paginateResults($query, $perPage);
    }

    // Menyamakan dengan struktur PengumumanModel
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Data berita
            $dataBerita = $request->t_berita;

            // Generate slug
            $kategoriBerita = BeritaDinamisModel::findOrFail($dataBerita['fk_m_berita_dinamis']);
            $kategoriSlug = Str::slug($kategoriBerita->bd_nama_submenu);
            $judulSlug = Str::slug($dataBerita['berita_judul']);
            $dataBerita['berita_slug'] = $kategoriSlug . '/' . $judulSlug;

            // Handle thumbnail upload
            if ($request->hasFile('berita_thumbnail')) {
                $thumbnailPath = $request->file('berita_thumbnail')->store('public/berita/thumbnails');
                $dataBerita['berita_thumbnail'] = str_replace('public/', '', $thumbnailPath);
            }

            // Simpan data berita
            $detailBerita = self::create($dataBerita);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $detailBerita->berita_id,
                $detailBerita->berita_judul
            );

            DB::commit();

            return self::responFormatSukses($detailBerita, 'Berita berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat berita');
        }
    }

    // Menyamakan dengan struktur PengumumanModel
    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            // Data berita
            $dataBerita = $request->t_berita;
            $detailBerita = self::findOrFail($id);

            // Generate slug
            $kategoriBerita = BeritaDinamisModel::findOrFail($dataBerita['fk_m_berita_dinamis']);
            $kategoriSlug = Str::slug($kategoriBerita->bd_nama_submenu);
            $judulSlug = Str::slug($dataBerita['berita_judul']);
            $dataBerita['berita_slug'] = $kategoriSlug . '/' . $judulSlug;

            // Handle thumbnail upload
            if ($request->hasFile('berita_thumbnail')) {
                // Hapus thumbnail lama jika ada
                if ($detailBerita->berita_thumbnail) {
                    Storage::delete('public/' . $detailBerita->berita_thumbnail);
                }

                $thumbnailPath = $request->file('berita_thumbnail')->store('public/berita/thumbnails');
                $dataBerita['berita_thumbnail'] = str_replace('public/', '', $thumbnailPath);
            }

            // Update data berita
            $detailBerita->update($dataBerita);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $detailBerita->berita_id,
                $detailBerita->berita_judul
            );

            DB::commit();

            return self::responFormatSukses($detailBerita, 'Berita berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui berita');
        }
    }

    // Menyamakan dengan struktur PengumumanModel
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $detailBerita = self::findOrFail($id);

            // Hapus thumbnail jika ada
            if ($detailBerita->berita_thumbnail) {
                Storage::delete('public/' . $detailBerita->berita_thumbnail);
            }

            // Hapus data berita (soft delete)
            $detailBerita->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $detailBerita->berita_id,
                $detailBerita->berita_judul
            );

            DB::commit();

            return self::responFormatSukses($detailBerita, 'Berita berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus berita');
        }
    }

    public static function detailData($id)
    {
        return self::with('BeritaDinamis')->findOrFail($id);
    }

    // Menyamakan struktur validasiData dengan PengumumanModel
    public static function validasiData($request, $id = null)
    {
        $rules = [];
        $messages = [];

        // Validasi kategori berita
        $rules['t_berita.fk_m_berita_dinamis'] = 'required|exists:m_berita_dinamis,berita_dinamis_id';
        $messages['t_berita.fk_m_berita_dinamis.required'] = 'Kategori berita wajib dipilih';
        $messages['t_berita.fk_m_berita_dinamis.exists'] = 'Kategori berita tidak valid';

        // Validasi judul berita
        $rules['t_berita.berita_judul'] = 'required|max:140';
        $messages['t_berita.berita_judul.required'] = 'Judul berita wajib diisi';
        $messages['t_berita.berita_judul.max'] = 'Judul berita maksimal 140 karakter';

        // Validasi status berita
        $rules['t_berita.status_berita'] = 'required|in:aktif,nonaktif';
        $messages['t_berita.status_berita.required'] = 'Status berita wajib dipilih';
        $messages['t_berita.status_berita.in'] = 'Status berita tidak valid';

        // Validasi thumbnail
        if ($request->hasFile('berita_thumbnail')) {
            $rules['berita_thumbnail'] = 'image|mimes:jpeg,png,jpg,gif|max:2560'; // 2.5 MB
            $messages['berita_thumbnail.image'] = 'Thumbnail harus berupa gambar';
            $messages['berita_thumbnail.mimes'] = 'Thumbnail harus berformat jpeg, png, jpg, atau gif';
            $messages['berita_thumbnail.max'] = 'Ukuran thumbnail maksimal 2.5 MB';
        }

        // Validasi deskripsi thumbnail
        $rules['t_berita.berita_thumbnail_deskripsi'] = 'nullable|max:255';
        $messages['t_berita.berita_thumbnail_deskripsi.max'] = 'Deskripsi thumbnail maksimal 255 karakter';

        // Validasi konten berita
        $rules['t_berita.berita_deskripsi'] = 'required';
        $messages['t_berita.berita_deskripsi.required'] = 'Konten berita wajib diisi';

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}