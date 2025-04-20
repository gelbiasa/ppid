<?php

namespace App\Models\Website\LandingPage\MediaDinamis;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class DetailMediaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_media_dinamis';
    protected $primaryKey = 'detail_media_dinamis_id';
    protected $fillable = [
        'fk_m_media_dinamis',
        'dm_type_media',
        'dm_judul_media',
        'dm_media_upload',
        'status_media'
    ];

    public function mediaDinamis()
    {
        return $this->belongsTo(MediaDinamisModel::class, 'fk_m_media_dinamis', 'media_dinamis_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }


    public static function selectData($perPage = null, $search = '', $kategori = null)
    {
        $query = self::with('mediaDinamis')
            ->where('isDeleted', 0);

        // Filter by kategori 
        if (!empty($kategori)) {
            $query->where('fk_m_media_dinamis', $kategori);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('dm_media_upload', 'like', "%{$search}%")
                    ->orWhere('dm_type_media', 'like', "%{$search}%")
                    ->orWhereHas('mediaDinamis', function ($subQuery) use ($search) {
                        $subQuery->where('md_kategori_media', 'like', "%{$search}%");
                    });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $detailMediaDinamisFile = self::uploadFile(
            $request->file('media_file'),
            'media_dinamis'
        );
    
        try {
            DB::beginTransaction();
    
            $data = $request->t_detail_media_dinamis;
            
            // Jika file diupload dan tipe media adalah file
            if ($detailMediaDinamisFile && $data['dm_type_media'] == 'file') {
                $data['dm_media_upload'] = $detailMediaDinamisFile;
            }
    
            $detailMediaDinamis = self::create($data);
    
            TransactionModel::createData(
                'CREATED',
                $detailMediaDinamis->detail_media_dinamis_id,
                $detailMediaDinamis->dm_judul_media
            );
            $result = self::responFormatSukses($detailMediaDinamis, 'Detail Media Dinamis berhasil dibuat');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($detailMediaDinamisFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($detailMediaDinamisFile);
            return self::responFormatError($e, 'Gagal membuat Detail Media Dinamis');
        }
    }
    
    public static function updateData($request, $id)
    {
        $detailMediaDinamisFile = self::uploadFile(
            $request->file('media_file'),
            'media_dinamis'
        );
    
        try {
            DB::beginTransaction();
    
            $detailMediaDinamis = self::findOrFail($id);
            $data = $request->t_detail_media_dinamis;
    
            // Jika file diupload dan tipe media adalah file
            if ($detailMediaDinamisFile && $data['dm_type_media'] == 'file') {
                // Hapus file lama jika ada
                if ($detailMediaDinamis->dm_media_upload && $detailMediaDinamis->dm_type_media == 'file') {
                    self::removeFile($detailMediaDinamis->dm_media_upload);
                }
    
                $data['dm_media_upload'] = $detailMediaDinamisFile;
            } 
            // Jika tidak ada upload file baru tetapi tipe media berubah dari link ke file
            elseif ($data['dm_type_media'] == 'file' && $detailMediaDinamis->dm_type_media == 'link') {
                $data['dm_media_upload'] = $detailMediaDinamis->dm_media_upload;
            }
    
            $detailMediaDinamis->update($data);
    
            TransactionModel::createData(
                'UPDATED',
                $detailMediaDinamis->detail_media_dinamis_id,
                $detailMediaDinamis->dm_judul_media
            );
            $result = self::responFormatSukses($detailMediaDinamis, 'Detail Media Dinamis berhasil diperbarui');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($detailMediaDinamisFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($detailMediaDinamisFile);
            return self::responFormatError($e, 'Gagal memperbarui Detail Media Dinamis');
        }
    }
    
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
    
            $detailMediaDinamis = self::findOrFail($id);
    
            // Jika tipe media adalah file, hapus file fisik
            if ($detailMediaDinamis->dm_type_media == 'file' && $detailMediaDinamis->dm_media_upload) {
                self::removeFile($detailMediaDinamis->dm_media_upload);
            }
    
            $detailMediaDinamis->delete();
    
            TransactionModel::createData(
                'DELETED',
                $detailMediaDinamis->detail_media_dinamis_id,
                $detailMediaDinamis->dm_judul_media
            );
    
            DB::commit();
    
            return self::responFormatSukses($detailMediaDinamis, 'Detail Media Dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Detail Media Dinamis');
        }
    } 


    public static function detailData($id)
    {
        return self::with('mediaDinamis')->findOrFail($id);
    }



    public static function validasiData($request)
    {
        $rules = [
            't_detail_media_dinamis.fk_m_media_dinamis' => 'required|exists:m_media_dinamis,media_dinamis_id',
            't_detail_media_dinamis.dm_judul_media' => 'required|max:100',
            't_detail_media_dinamis.dm_type_media' => 'required|in:file,link',
            't_detail_media_dinamis.status_media' => 'required|in:aktif,nonaktif',
        ];
    
        // Validasi URL jika tipe media adalah link
        if ($request->input('t_detail_media_dinamis.dm_type_media') === 'link') {
            $rules['t_detail_media_dinamis.dm_media_upload'] = 'required|url|max:200';
        }
    
        // Validasi file jika tipe media adalah file
        if ($request->hasFile('media_file')) {
            $rules['media_file'] = [
                'file',
                'mimes:jpg,jpeg,png,gif,svg,webp,pdf',
                'max:2560'
            ];
        }
    
        $messages = [
            't_detail_media_dinamis.fk_m_media_dinamis.required' => 'Kategori media wajib dipilih',
            't_detail_media_dinamis.fk_m_media_dinamis.exists' => 'Kategori media tidak valid',
            't_detail_media_dinamis.dm_judul_media.required' => 'Judul media wajib diisi',
            't_detail_media_dinamis.dm_judul_media.max' => 'Judul media maksimal 100 karakter',
            't_detail_media_dinamis.dm_type_media.required' => 'Tipe media wajib diisi',
            't_detail_media_dinamis.dm_type_media.in' => 'Tipe media harus berupa file atau link',
            't_detail_media_dinamis.dm_media_upload.required' => 'URL media wajib diisi',
            't_detail_media_dinamis.dm_media_upload.url' => 'URL media harus berupa URL yang valid',
            't_detail_media_dinamis.dm_media_upload.max' => 'URL media maksimal 200 karakter',
            't_detail_media_dinamis.status_media.required' => 'Status media wajib dipilih',
            't_detail_media_dinamis.status_media.in' => 'Status media harus berupa aktif atau nonaktif',
            'media_file.file' => 'File media harus berupa file',
            'media_file.mimes' => 'File media hanya boleh berupa: jpg, jpeg, png, gif, svg, webp, atau pdf',
            'media_file.max' => 'Ukuran file media maksimal 2.5MB',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    
        return true;
    }
}