<?php

namespace App\Models\Website\LandingPage\MediaDinamis;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MediaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_media_dinamis';
    protected $primaryKey = 'media_dinamis_id';
    protected $fillable = [
        'md_kategori_media',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    // function untuk API Hero section
    public static function getDataHeroSection()
    {
        $arr_data = self::query()
            ->from('m_media_dinamis')
            ->select([
                'media_dinamis_id',
                'md_kategori_media'
            ])
            ->where('media_dinamis_id', 1)
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($kategori) {
                // Ambil Detail Media untuk Hero Section
                $heroMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                ->where('isDeleted', 0)
                ->where('status_media', 'aktif')
                ->orderBy('detail_media_dinamis_id', 'desc')
                ->limit(4)
                ->get()
                ->map(function ($media) {
                    // Cek tipe media
                    if ($media->dm_type_media == 'file') {
                        return asset('storage/' . $media->dm_media_upload);
                    } elseif ($media->dm_type_media == 'link') {
                        return $media->dm_media_upload; // Kembalikan link asli
                    }
                    return null;
                })
                ->filter() 
                ->toArray();
    
                return [
                    'kategori_id' => $kategori->media_dinamis_id,
                    'kategori_nama' => $kategori->md_kategori_media,
                    'media' => $heroMedia
                ];
            })
            ->toArray();
    
        return $arr_data;
    }
    public static function getDataDokumentasi()
    {
        $arr_data = self::query()
            ->from('m_media_dinamis')
            ->select([
                'media_dinamis_id',
                'md_kategori_media'
            ])
            ->where('media_dinamis_id', 2)
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($kategori) {
                // Ambil Detail Media untuk Dokumentasi
                $dokumentasiMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                    ->where('isDeleted', 0)
                    ->where('status_media', 'aktif')
                    ->orderBy('detail_media_dinamis_id', 'desc')
                    ->limit(6)
                    ->get()
                    ->map(function ($media) {
                        // Cek tipe media
                        if ($media->dm_type_media == 'file') {
                            return asset('storage/' . $media->dm_media_upload);
                        } elseif ($media->dm_type_media == 'link') {
                            return $media->dm_media_upload; // Kembalikan link asli
                        }
                        return null;
                    })
                    ->filter() 
                    ->toArray();
    
                return [
                    'kategori_id' => $kategori->media_dinamis_id,
                    'kategori_nama' => $kategori->md_kategori_media,
                    'media' => $dokumentasiMedia
                ];
            })
            ->toArray();
    
        return $arr_data;
    }
    public static function getDataMediaInformasiPublik()
    {
        $arr_data = self::query()
            ->from('m_media_dinamis')
            ->select([
                'media_dinamis_id',
                'md_kategori_media'
            ])
            ->where('media_dinamis_id', 3)
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($kategori) {
                // Ambil Detail Media untuk Dokumentasi
                $dokumentasiMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                    ->where('isDeleted', 0)
                    ->where('status_media', 'aktif')
                    ->orderBy('detail_media_dinamis_id', 'desc')
                    ->limit(2)
                    ->get()
                    ->map(function ($media) {
                        return [
                            'judul_media' => $media->dm_judul_media,
                            'media_url' => $media->dm_type_media == 'file'
                                ? asset('storage/' . $media->dm_media_upload)
                                : ($media->dm_type_media == 'link' ? $media->dm_media_upload : null)
                        ];
                    })
                    ->filter()
                    ->toArray();
    
                return [
                    'kategori_id' => $kategori->media_dinamis_id,
                    'kategori_nama' => $kategori->md_kategori_media,
                    'media' => $dokumentasiMedia
                ];
            })
            ->toArray();
    
        return $arr_data;
    }
    
    
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Add search functionality
        if (!empty($search)) {
            $query->where('md_kategori_media', 'like', "%{$search}%");
        }

        return self::paginateResults($query, $perPage);
    }


    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_media_dinamis;
            $mediaDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );

            DB::commit();

            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat media dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $mediaDinamis = self::findOrFail($id);

            $data = $request->m_media_dinamis;
            $mediaDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );

            DB::commit();

            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui media dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
    
            $mediaDinamis = self::findOrFail($id);
    
            $mediaTerkait = DetailMediaDinamisModel::where('fk_m_media_dinamis', $id)
                ->where('isDeleted', 0)
                ->count();
    
            if ($mediaTerkait > 0) {
                throw new \Exception('Masih terdapat footer aktif yang terkait');
            }
    
            $mediaDinamis->delete();
    
            TransactionModel::createData(
                'DELETED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );
    
            DB::commit();
    
            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, $e->getMessage());
        }
    }
    

    
    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_media_dinamis.md_kategori_media' => 'required|max:100',
        ];

        $messages = [
            'm_media_dinamis.md_kategori_media.required' => 'Kategori media wajib diisi',
            'm_media_dinamis.md_kategori_media.max' => 'Kategori media maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}