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

    // Relationship with Berita
    public function berita()
    {
        return $this->belongsTo(BeritaModel::class, 'fk_t_berita', 'berita_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Method to select uploaded files/images
    public static function selectData($beritaId = null, $perPage = 10)
    {
        $query = self::query()
            ->where('isDeleted', 0);

        if ($beritaId) {
            $query->where('fk_t_berita', $beritaId);
        }

        return $query->paginate($perPage);
    }

    // Method to create upload record
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validate input
            self::validasiData($request);

            // Prepare data
            $data = [
                'fk_t_berita' => $request->berita_id,
                'ub_type' => $request->type,
                'ub_value' => $request->value
            ];

            // Create upload record
            $uploadBerita = self::create($data);

            // Log transaction
            TransactionModel::createData(
                'CREATED',
                $uploadBerita->upload_berita_id,
                $uploadBerita->ub_type . ' - ' . $uploadBerita->ub_value
            );

            DB::commit();
            return $uploadBerita;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Validation method
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'berita_id' => 'required|exists:t_berita,berita_id',
            'type' => 'required|in:file,link',
            'value' => [
                'required',
                function($attribute, $value, $fail) use ($request) {
                    $type = $request->type;
                    
                    if ($type === 'link' && !filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Format link tidak valid');
                    }
                    
                    if ($type === 'file') {
                        // Validasi ukuran file (maks 2.5 MB)
                        $maxSize = 2.5 * 1024; // 2.5 MB
                        
                        if (is_file($value)) {
                            $fileSize = $value->getSize() / 1024; // Convert to KB
                            
                            if ($fileSize > $maxSize) {
                                $fail('Ukuran file tidak boleh lebih dari 2.5 MB');
                            }
                        }
                    }
                }
            ]
        ];

        $messages = [
            'berita_id.required' => 'Berita wajib dipilih',
            'berita_id.exists' => 'Berita tidak valid',
            'type.required' => 'Tipe upload wajib dipilih',
            'type.in' => 'Tipe upload hanya boleh file atau link',
            'value.required' => 'Nilai upload wajib diisi'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    // Method untuk menghapus upload
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $uploadBerita = self::findOrFail($id);
            
            // Soft delete
            $uploadBerita->isDeleted = 1;
            $uploadBerita->deleted_at = now();
            $uploadBerita->save();

            $uploadBerita->delete();

            // Log transaksi
            TransactionModel::createData(
                'DELETED',
                $uploadBerita->upload_berita_id,
                $uploadBerita->ub_type . ' - ' . $uploadBerita->ub_value
            );
                
            DB::commit();

            return $uploadBerita;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}