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


    public static function selectData($perPage = 10, $search = '', $kategori = null)
    {
        $query = self::with('mediaDinamis')
            ->where('isDeleted', 0);

        // Filter by kategori if provided
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
        try {
            DB::beginTransaction();

            $data = $request->t_detail_media_dinamis;
            $detailMediaDinamis = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $detailMediaDinamis->detail_media_dinamis_id,
                $detailMediaDinamis->dm_media_upload
            );

            DB::commit();

            return self::responFormatSukses($detailMediaDinamis, 'Detail media dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat detail media dinamis');
        }
    }

    public static function updateData($request, $id)
    {
     
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $detailMediaDinamis = self::findOrFail($id);

            $detailMediaDinamis->delete();

            TransactionModel::createData(
                'DELETED',
                $detailMediaDinamis->detail_media_dinamis_id,
                $detailMediaDinamis->dm_media_upload
            );

            DB::commit();

            return self::responFormatSukses($detailMediaDinamis, 'Detail media dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus detail media dinamis');
        }
    }


    public static function detailData($id)
    {
        return self::with('mediaDinamis')->findOrFail($id);
    }


    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_detail_media_dinamis.fk_m_media_dinamis' => 'required|exists:m_media_dinamis,media_dinamis_id',
            't_detail_media_dinamis.dm_type_media' => 'required|in:file,link',
            't_detail_media_dinamis.dm_judul_media' => 'required|max:100',
            't_detail_media_dinamis.status_media' => 'required|in:aktif,nonaktif',
        ];

        // Tambahkan validasi untuk media_upload berdasarkan tipe
        if (isset($request->t_detail_media_dinamis['dm_type_media'])) {
            if ($request->t_detail_media_dinamis['dm_type_media'] == 'link') {
                $rules['t_detail_media_dinamis.dm_media_upload'] = 'required|max:200';
            } else if ($request->t_detail_media_dinamis['dm_type_media'] == 'file') {
                // Jika sedang update, file upload tidak wajib
                if ($id) {
                    $rules['media_file'] = 'nullable|file|max:2560|mimes:jpg,jpeg,png,gif,svg,webp,pdf';
                } else {
                    // Saat create, file wajib
                    $rules['media_file'] = 'required|file|max:2560|mimes:jpg,jpeg,png,gif,svg,webp,pdf';
                }
            }
        }

        $messages = [
            't_detail_media_dinamis.fk_m_media_dinamis.required' => 'Media dinamis wajib dipilih',
            't_detail_media_dinamis.fk_m_media_dinamis.exists' => 'Media dinamis tidak valid',
            't_detail_media_dinamis.dm_type_media.required' => 'Tipe media wajib diisi',
            't_detail_media_dinamis.dm_type_media.in' => 'Tipe media harus berupa file atau link',
            't_detail_media_dinamis.dm_media_upload.required' => 'Media upload wajib diisi',
            't_detail_media_dinamis.dm_media_upload.max' => 'Media upload maksimal 255 karakter',
            't_detail_media_dinamis.status_media.required' => 'Status media wajib dipilih',
            't_detail_media_dinamis.status_media.in' => 'Status media harus aktif atau nonaktif',
            'media_file.required' => 'File media wajib diupload',
            'media_file.file' => 'Upload harus berupa file',
            'media_file.max' => 'Ukuran file maksimal 2.5MB',
            'media_file.mimes' => 'Format file harus jpg, jpeg, png, gif, svg, webp, atau pdf',
            't_detail_media_dinamis.dm_judul_media.required' => 'Judul media wajib diisi',
            't_detail_media_dinamis.dm_judul_media.max' => 'Judul media maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}