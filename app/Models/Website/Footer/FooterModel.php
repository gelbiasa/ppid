<?php

namespace App\Models\Website\Footer;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FooterModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 't_footer';
    protected $primaryKey = 'footer_id';
    protected $fillable = [
        'fk_m_kategori_footer',
        'f_judul_footer',
        'f_icon_footer',
        'f_url_footer',
    ];

    // Relasi dengan kategori footer
    public function kategoriFooter()
    {
        return $this->belongsTo(KategoriFooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
    }

    // Metode untuk select data dengan pagination dan filter
    public static function selectData($request = null)
    {
        $query = self::with('kategoriFooter')->where('isDeleted', 0);

        // Filter berdasarkan pencarian
        if ($request && $request->has('search')) {
            $query->where('f_judul_footer', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request && $request->has('kategori')) {
            $query->where('fk_m_kategori_footer', $request->kategori);
        }

        // Sorting dan pagination
        return $query->paginate($request->perPage ?? 10);
    }

    // Metode create data
    public static function createData($request)
    {
        // Validasi input
        self::validasiData($request);

        // Persiapan data
        $data = $request->only([
            'fk_m_kategori_footer', 
            'f_judul_footer', 
            'f_url_footer'
        ]);

        // Proses upload ikon
        if ($request->hasFile('f_icon_footer')) {
            $iconPath = $request->file('f_icon_footer')->store('footer_icons', 'public');
            $data['f_icon_footer'] = $iconPath;
        }

        // Buat record
        return self::create($data);
    }

    // Metode update data
    public static function updateData($request, $id)
    {
        // Cari record
        $model = self::findOrFail($id);

        // Validasi input
        self::validasiData($request, $id);

        // Persiapan data
        $data = $request->only([
            'fk_m_kategori_footer', 
            'f_judul_footer', 
            'f_url_footer'
        ]);

        // Proses upload ikon
        if ($request->hasFile('f_icon_footer')) {
            // Hapus ikon lama jika ada
            if ($model->f_icon_footer) {
                Storage::disk('public')->delete($model->f_icon_footer);
            }

            // Upload ikon baru
            $iconPath = $request->file('f_icon_footer')->store('footer_icons', 'public');
            $data['f_icon_footer'] = $iconPath;
        }

        // Update record
        $model->update($data);

        return $model;
    }

    // Metode hapus data (soft delete)
    public static function deleteData($id)
    {
        $model = self::findOrFail($id);

        // Hapus file ikon jika ada
        if ($model->f_icon_footer) {
            Storage::disk('public')->delete($model->f_icon_footer);
        }

        // Soft delete akan dihandle oleh BaseModel
        return $model->delete();
    }

    // Validasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'fk_m_kategori_footer' => 'required|exists:m_kategori_footer,kategori_footer_id',
            'f_judul_footer' => 'required|max:100',
            'f_url_footer' => 'nullable|url|max:100',
            'f_icon_footer' => 'nullable|image|max:2048', // Maks 2MB
        ];

        $messages = [
            'fk_m_kategori_footer.required' => 'Kategori footer wajib dipilih',
            'fk_m_kategori_footer.exists' => 'Kategori footer tidak valid',
            'f_judul_footer.required' => 'Judul footer wajib diisi',
            'f_judul_footer.max' => 'Judul footer maksimal 100 karakter',
            'f_url_footer.url' => 'URL footer harus berupa URL yang valid',
            'f_url_footer.max' => 'URL footer maksimal 100 karakter',
            'f_icon_footer.image' => 'Ikon harus berupa gambar',
            'f_icon_footer.max' => 'Ukuran ikon maksimal 2MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // Metode untuk mendapatkan data footer untuk tampilan frontend
    public static function getFooterData()
    {
        return self::with('kategoriFooter')
            ->where('isDeleted', 0)
            ->orderBy('fk_m_kategori_footer')
            ->get()
            ->groupBy('fk_m_kategori_footer');
    }
}