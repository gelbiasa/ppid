<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PengumumanModel extends Model
{
    use TraitsModel;

    protected $table = 't_pengumuman';
    protected $primaryKey = 'pengumuman_id';
    protected $fillable = [
        'fk_m_pengumuman_dinamis',
        'peg_judul',
        'peg_slug',
        'status_pengumuman',
    ];

    public function PengumumanDinamis()
    {
        return $this->belongsTo(PengumumanDinamisModel::class, 'fk_m_pengumuman_dinamis', 'pengumuman_dinamis_id');
    }
    
    public function UploadPengumuman()
    {
        return $this->hasOne(UploadPengumumanModel::class, 'fk_t_pengumuman', 'pengumuman_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        return self::with(['PengumumanDinamis', 'UploadPengumuman'])
            ->where('isDeleted', 0)
            ->get();
    }
    public static function getDataPengumumanLandingPage()
    {
        $arr_data = self::query()
            ->select([
                'pengumuman_id',
                'peg_judul',
                'peg_slug',
                'fk_m_pengumuman_dinamis',
                'created_at'
            ])
            ->with(['PengumumanDinamis', 'UploadPengumuman'])
            ->whereHas('PengumumanDinamis', function($query) {
                $query->where('isDeleted', 0)
                      ->where('pd_nama_submenu', 'Pengumuman PPID');
            })
            ->where('isDeleted', 0)
            ->where('status_pengumuman', 'aktif')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($pengumuman) {
                // Proses thumbnail
                $thumbnail = null;
                $uploadPengumuman = $pengumuman->UploadPengumuman;
                
                if ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                    $thumbnail = asset('storage/' . $uploadPengumuman->up_thumbnail);
                }
    
                // Proses value 
                $value = $uploadPengumuman ? $uploadPengumuman->up_value : null;
                if ($uploadPengumuman && $uploadPengumuman->up_type === 'file') {
                    $value = asset('storage/' . $value);
                }
                
                // Format tanggal sesuai desain
                $formattedDate = \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d F Y');
    
                return [
                    'id' => $pengumuman->pengumuman_id,
                    'judul' => $pengumuman->peg_judul,
                    'slug' => $pengumuman->peg_slug,
                    'submenu' => $pengumuman->PengumumanDinamis->pd_nama_submenu,
                    'thumbnail' => $thumbnail,
                    'tipe' => $uploadPengumuman ? $uploadPengumuman->up_type : null,
                    'value' => $value,
                    'konten' => $uploadPengumuman ? $uploadPengumuman->up_konten : null,
                    'created_at' => $formattedDate
                ];
            })
            ->toArray();
    
        return $arr_data;
    }
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Data pengumuman
            $dataPengumuman = $request->t_pengumuman;
            
            // Generate slug
            $kategoriPengumuman = PengumumanDinamisModel::findOrFail($dataPengumuman['fk_m_pengumuman_dinamis']);
            $kategoriSlug = Str::slug($kategoriPengumuman->pd_nama_submenu);
            
            // Jika judul tidak ada (tipe link), slug hanya dari kategori
            if (empty($dataPengumuman['peg_judul'])) {
                $dataPengumuman['peg_slug'] = $kategoriSlug . '/';
            } else {
                // Jika ada judul, tambahkan ke slug
                $judulSlug = Str::slug($dataPengumuman['peg_judul']);
                $dataPengumuman['peg_slug'] = $kategoriSlug . '/' . $judulSlug;
            }
            
            // Simpan data pengumuman
            $pengumuman = self::create($dataPengumuman);
            
            // Proses upload pengumuman
            $dataUpload = [
                'fk_t_pengumuman' => $pengumuman->pengumuman_id,
                'up_type' => $request->up_type,
            ];
            
            // Handle berdasarkan tipe konten
            switch ($request->up_type) {
                case 'link':
                    $dataUpload['up_value'] = $request->up_value;
                    $dataUpload['up_thumbnail'] = null;
                    $dataUpload['up_konten'] = null;
                    break;
                    
                case 'file':
                    if ($request->hasFile('up_value')) {
                        $dataUpload['up_value'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_value'), 
                            'up_value'
                        );
                    }
                    
                    if ($request->hasFile('up_thumbnail')) {
                        $dataUpload['up_thumbnail'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_thumbnail'), 
                            'up_thumbnail'
                        );
                    }
                    
                    $dataUpload['up_konten'] = null;
                    break;
                    
                case 'konten':
                    $dataUpload['up_value'] = null;
                    
                    if ($request->hasFile('up_thumbnail')) {
                        $dataUpload['up_thumbnail'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_thumbnail'), 
                            'up_thumbnail'
                        );
                    }
                    
                    $dataUpload['up_konten'] = $request->up_konten;
                    break;
            }
            
            // Simpan data upload
            UploadPengumumanModel::create($dataUpload);
            
            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $pengumuman->pengumuman_id,
                $pengumuman->peg_judul ?? $kategoriPengumuman->pd_nama_submenu
            );
            
            DB::commit();
            
            return self::responFormatSukses($pengumuman, 'Pengumuman berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat pengumuman');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Data pengumuman
            $dataPengumuman = $request->t_pengumuman;
            $pengumuman = self::findOrFail($id);
            
            // Generate slug
            $kategoriPengumuman = PengumumanDinamisModel::findOrFail($dataPengumuman['fk_m_pengumuman_dinamis']);
            $kategoriSlug = Str::slug($kategoriPengumuman->pd_nama_submenu);
            
            // Jika judul tidak ada (tipe link), slug hanya dari kategori
            if (empty($dataPengumuman['peg_judul'])) {
                $dataPengumuman['peg_slug'] = $kategoriSlug . '/';
            } else {
                // Jika ada judul, tambahkan ke slug
                $judulSlug = Str::slug($dataPengumuman['peg_judul']);
                $dataPengumuman['peg_slug'] = $kategoriSlug . '/' . $judulSlug;
            }
            
            // Update data pengumuman
            $pengumuman->update($dataPengumuman);
            
            // Ambil data upload pengumuman
            $uploadPengumuman = $pengumuman->UploadPengumuman;
            
            if (!$uploadPengumuman) {
                // Jika tidak ada data upload, buat baru
                $dataUpload = [
                    'fk_t_pengumuman' => $pengumuman->pengumuman_id,
                    'up_type' => $request->up_type,
                ];
            } else {
                // Jika sudah ada data upload, perbarui
                $dataUpload = [
                    'up_type' => $request->up_type,
                ];
                
                // Hapus file lama jika tipe berubah
                if ($uploadPengumuman->up_type != $request->up_type) {
                    // Hapus thumbnail lama jika ada
                    if ($uploadPengumuman->up_thumbnail) {
                        UploadPengumumanModel::removeFile($uploadPengumuman->up_thumbnail);
                    }
                    
                    // Hapus file lama jika ada
                    if ($uploadPengumuman->up_value) {
                        UploadPengumumanModel::removeFile($uploadPengumuman->up_value);
                    }
                }
            }
            
            // Handle berdasarkan tipe konten
            switch ($request->up_type) {
                case 'link':
                    $dataUpload['up_value'] = $request->up_value;
                    
                    // Hapus thumbnail lama jika ada
                    if ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                        UploadPengumumanModel::removeFile($uploadPengumuman->up_thumbnail);
                    }
                    
                    $dataUpload['up_thumbnail'] = null;
                    $dataUpload['up_konten'] = null;
                    break;
                    
                case 'file':
                    if ($request->hasFile('up_value')) {
                        // Hapus file lama jika ada
                        if ($uploadPengumuman && $uploadPengumuman->up_value) {
                            UploadPengumumanModel::removeFile($uploadPengumuman->up_value);
                        }
                        
                        $dataUpload['up_value'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_value'), 
                            'up_value'
                        );
                    } elseif ($uploadPengumuman && $uploadPengumuman->up_value && $uploadPengumuman->up_type == 'file') {
                        // Jika tidak ada file baru, gunakan file lama
                        $dataUpload['up_value'] = $uploadPengumuman->up_value;
                    }
                    
                    if ($request->hasFile('up_thumbnail')) {
                        // Hapus thumbnail lama jika ada
                        if ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                            UploadPengumumanModel::removeFile($uploadPengumuman->up_thumbnail);
                        }
                        
                        $dataUpload['up_thumbnail'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_thumbnail'), 
                            'up_thumbnail'
                        );
                    } elseif ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                        // Jika tidak ada thumbnail baru, gunakan thumbnail lama
                        $dataUpload['up_thumbnail'] = $uploadPengumuman->up_thumbnail;
                    }
                    
                    $dataUpload['up_konten'] = null;
                    break;
                    
                case 'konten':
                    // Hapus file lama jika ada dan tipe berubah
                    if ($uploadPengumuman && $uploadPengumuman->up_value && $uploadPengumuman->up_type != 'konten') {
                        UploadPengumumanModel::removeFile($uploadPengumuman->up_value);
                    }
                    
                    $dataUpload['up_value'] = null;
                    
                    if ($request->hasFile('up_thumbnail')) {
                        // Hapus thumbnail lama jika ada
                        if ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                            UploadPengumumanModel::removeFile($uploadPengumuman->up_thumbnail);
                        }
                        
                        $dataUpload['up_thumbnail'] = UploadPengumumanModel::uploadFile(
                            $request->file('up_thumbnail'), 
                            'up_thumbnail'
                        );
                    } elseif ($uploadPengumuman && $uploadPengumuman->up_thumbnail) {
                        // Jika tidak ada thumbnail baru, gunakan thumbnail lama
                        $dataUpload['up_thumbnail'] = $uploadPengumuman->up_thumbnail;
                    }
                    
                    $dataUpload['up_konten'] = $request->up_konten;
                    break;
            }
            
            // Update atau buat data upload
            if ($uploadPengumuman) {
                $uploadPengumuman->update($dataUpload);
            } else {
                UploadPengumumanModel::create($dataUpload);
            }
            
            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $pengumuman->pengumuman_id,
                $pengumuman->peg_judul ?? $kategoriPengumuman->pd_nama_submenu
            );
            
            DB::commit();
            
            return self::responFormatSukses($pengumuman, 'Pengumuman berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui pengumuman');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $pengumuman = self::with(['UploadPengumuman'])->findOrFail($id);
            
            // Hapus file terkait jika ada
            if ($pengumuman->UploadPengumuman) {
                $uploadPengumuman = $pengumuman->UploadPengumuman;
                
                // Hapus thumbnail jika ada
                if ($uploadPengumuman->up_thumbnail) {
                    UploadPengumumanModel::removeFile($uploadPengumuman->up_thumbnail);
                }
                
                // Hapus file jika ada
                if ($uploadPengumuman->up_value) {
                    UploadPengumumanModel::removeFile($uploadPengumuman->up_value);
                }
                
                // Hapus data upload
                $uploadPengumuman->delete();
            }
            
            // Hapus data pengumuman
            $pengumuman->delete();
            
            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $pengumuman->pengumuman_id,
                $pengumuman->peg_judul ?? $pengumuman->PengumumanDinamis->pd_nama_submenu
            );
            
            DB::commit();
            
            return self::responFormatSukses($pengumuman, 'Pengumuman berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus pengumuman');
        }
    }

    public static function detailData($id)
    {
        return self::with(['PengumumanDinamis', 'UploadPengumuman'])->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [];
        $messages = [];
        
        // Validasi kategori pengumuman
        $rules['t_pengumuman.fk_m_pengumuman_dinamis'] = 'required|exists:m_pengumuman_dinamis,pengumuman_dinamis_id';
        $messages['t_pengumuman.fk_m_pengumuman_dinamis.required'] = 'Kategori pengumuman wajib dipilih';
        $messages['t_pengumuman.fk_m_pengumuman_dinamis.exists'] = 'Kategori pengumuman tidak valid';
        
        // Validasi status pengumuman
        $rules['t_pengumuman.status_pengumuman'] = 'required|in:aktif,tidak aktif';
        $messages['t_pengumuman.status_pengumuman.required'] = 'Status pengumuman wajib dipilih';
        $messages['t_pengumuman.status_pengumuman.in'] = 'Status pengumuman tidak valid';
        
        // Validasi tipe konten
        $rules['up_type'] = 'required|in:link,file,konten';
        $messages['up_type.required'] = 'Tipe pengumuman wajib dipilih';
        $messages['up_type.in'] = 'Tipe pengumuman tidak valid';
        
        // Validasi berdasarkan tipe konten
        switch ($request->up_type) {
            case 'link':
                $rules['up_value'] = 'required|url|max:255';
                $messages['up_value.required'] = 'URL wajib diisi';
                $messages['up_value.url'] = 'URL tidak valid';
                $messages['up_value.max'] = 'URL maksimal 255 karakter';
                break;
                
            case 'file':
                // Validasi judul
                $rules['t_pengumuman.peg_judul'] = 'required|max:255';
                $messages['t_pengumuman.peg_judul.required'] = 'Judul pengumuman wajib diisi';
                $messages['t_pengumuman.peg_judul.max'] = 'Judul pengumuman maksimal 255 karakter';
                
                // Validasi thumbnail
                if (!$request->hasFile('up_thumbnail') && $request->pengumuman_id === null) {
                    $rules['up_thumbnail'] = 'required|image|mimes:jpeg,png,jpg,gif|max:10240';
                    $messages['up_thumbnail.required'] = 'Thumbnail wajib diunggah';
                } elseif ($request->hasFile('up_thumbnail')) {
                    $rules['up_thumbnail'] = 'image|mimes:jpeg,png,jpg,gif|max:10240';
                }
                
                $messages['up_thumbnail.image'] = 'Thumbnail harus berupa gambar';
                $messages['up_thumbnail.mimes'] = 'Thumbnail harus berformat jpeg, png, jpg, atau gif';
                $messages['up_thumbnail.max'] = 'Ukuran thumbnail maksimal 10 MB';
                
                // Validasi file
                if (!$request->hasFile('up_value') && $request->pengumuman_id === null) {
                    $rules['up_value'] = 'required|file|max:51200';
                    $messages['up_value.required'] = 'File wajib diunggah';
                } elseif ($request->hasFile('up_value')) {
                    $rules['up_value'] = 'file|max:51200';
                }
                
                $messages['up_value.file'] = 'File tidak valid';
                $messages['up_value.max'] = 'Ukuran file maksimal 50 MB';
                break;
                
            case 'konten':
                // Validasi judul
                $rules['t_pengumuman.peg_judul'] = 'required|max:255';
                $messages['t_pengumuman.peg_judul.required'] = 'Judul pengumuman wajib diisi';
                $messages['t_pengumuman.peg_judul.max'] = 'Judul pengumuman maksimal 255 karakter';
                
                // Validasi thumbnail
                if (!$request->hasFile('up_thumbnail') && $request->pengumuman_id === null) {
                    $rules['up_thumbnail'] = 'required|image|mimes:jpeg,png,jpg,gif|max:10240';
                    $messages['up_thumbnail.required'] = 'Thumbnail wajib diunggah';
                } elseif ($request->hasFile('up_thumbnail')) {
                    $rules['up_thumbnail'] = 'image|mimes:jpeg,png,jpg,gif|max:10240';
                }
                
                $messages['up_thumbnail.image'] = 'Thumbnail harus berupa gambar';
                $messages['up_thumbnail.mimes'] = 'Thumbnail harus berformat jpeg, png, jpg, atau gif';
                $messages['up_thumbnail.max'] = 'Ukuran thumbnail maksimal 10 MB';
                
                // Validasi konten
                $rules['up_konten'] = 'required';
                $messages['up_konten.required'] = 'Konten pengumuman wajib diisi';
                break;
        }
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }
}