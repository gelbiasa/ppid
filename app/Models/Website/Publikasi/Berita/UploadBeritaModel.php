<?php

namespace App\Models\Website\Publikasi\Berita;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UploadBeritaModel extends Model
{
    use TraitsModel;

    protected $table = 't_upload_berita';
    protected $primaryKey = 'upload_berita_id';
    protected $fillable = [
        'fk_t_berita',
        'ub_type',
        'ub_value'
    ];

    public function berita()
    {
        return $this->belongsTo(BeritaModel::class, 'fk_t_berita', 'berita_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0)
            ->with('berita');

        // Add search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('ub_type', 'like', "%{$search}%")
                  ->orWhere('ub_value', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->t_upload_berita;
            $uploadBerita = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $uploadBerita->upload_berita_id,
                $uploadBerita->ub_type . ' - ' . $uploadBerita->ub_value
            );

            DB::commit();

            return self::responFormatSukses($uploadBerita, 'Upload Berita berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Upload Berita');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $uploadBerita = self::findOrFail($id);
            
            $data = $request->t_upload_berita;
            $uploadBerita->update($data);

            TransactionModel::createData(
                'UPDATED',
                $uploadBerita->upload_berita_id,
                $uploadBerita->ub_type . ' - ' . $uploadBerita->ub_value
            );

            DB::commit();

            return self::responFormatSukses($uploadBerita, 'Upload Berita berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Upload Berita');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $uploadBerita = self::findOrFail($id);
            
            $uploadBerita->isDeleted = 1;
            $uploadBerita->deleted_at = now();
            $uploadBerita->save();

            $uploadBerita->delete();

            TransactionModel::createData(
                'DELETED',
                $uploadBerita->upload_berita_id,
                $uploadBerita->ub_type . ' - ' . $uploadBerita->ub_value
            );
                
            DB::commit();

            return self::responFormatSukses($uploadBerita, 'Upload Berita berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Upload Berita');
        }
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_upload_berita.fk_t_berita' => 'required|exists:t_berita,berita_id',
            't_upload_berita.ub_type' => 'required|in:file,link',
            't_upload_berita.ub_value' => [
                'required', 
                function($attribute, $value, $fail) use ($request) {
                    $type = $request->input('t_upload_berita.ub_type');
                    
                    if ($type === 'link' && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Format link tidak valid');
                    }
                    
                    if ($type === 'file') {
                        $maxSize = 2.5 * 1024; // 2.5 MB
                        $fileValidator = Validator::make(
                            ['file' => $value], 
                            ['file' => 'file|max:' . $maxSize]
                        );
                        
                        if ($fileValidator->fails()) {
                            $fail('File tidak valid atau terlalu besar');
                        }
                    }
                }
            ]
        ];

        $messages = [
            't_upload_berita.fk_t_berita.required' => 'Berita wajib dipilih',
            't_upload_berita.fk_t_berita.exists' => 'Berita tidak valid',
            't_upload_berita.ub_type.required' => 'Tipe upload wajib dipilih',
            't_upload_berita.ub_type.in' => 'Tipe upload hanya boleh file atau link',
            't_upload_berita.ub_value.required' => 'Nilai upload wajib diisi'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}