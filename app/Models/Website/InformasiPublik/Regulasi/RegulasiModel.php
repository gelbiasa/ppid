<?php

namespace App\Models\Website\InformasiPublik\Regulasi;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class RegulasiModel extends Model
{
    use TraitsModel;

    protected $table = 't_regulasi';
    protected $primaryKey = 'regulasi_id';
    protected $fillable = [
        'fk_t_kategori_regulasi',
        'reg_judul',
        'reg_sinopsis',
        'reg_tipe_dokumen',
        'reg_dokumen'
    ];

    public function KategoriRegulasi()
    {
        return $this->belongsTo(KategoriRegulasiModel::class, 'fk_t_kategori_regulasi',  'kategori_reg_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('KategoriRegulasi')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reg_judul', 'like', "%{$search}%")
                  ->orWhere('reg_sinopsis', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }
    public static function createData($request)
    {
        $regulasiFile = self::uploadFile(
            $request->file('reg_dokumen'),
            'dokumen_regulasi'
        );
    
        try {
            DB::beginTransaction();
    
            $data = $request->t_regulasi;
            
            // Jika file diupload dan tipe dokumen adalah file
            if ($regulasiFile && $data['reg_tipe_dokumen'] == 'file') {
                $data['reg_dokumen'] = $regulasiFile;
            }
    
            $regulasi = self::create($data);
    
            TransactionModel::createData(
                'CREATED',
                $regulasi->regulasi_id,
                $regulasi->reg_judul
            );
            $result = self::responFormatSukses($regulasi, 'Regulasi berhasil dibuat');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($regulasiFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($regulasiFile);
            return self::responFormatError($e, 'Gagal membuat regulasi');
        }
    }
    
    public static function updateData($request, $id)
    {
        $regulasiFile = self::uploadFile(
            $request->file('reg_dokumen'),
            'dokumen_regulasi'
        );
    
        try {
            DB::beginTransaction();
    
            $regulasi = self::findOrFail($id);
            $data = $request->t_regulasi;
    
            // Jika file diupload dan tipe dokumen adalah file
            if ($regulasiFile && $data['reg_tipe_dokumen'] == 'file') {
                // Hapus file lama jika ada
                if ($regulasi->reg_dokumen && $regulasi->reg_tipe_dokumen == 'file') {
                    self::removeFile($regulasi->reg_dokumen);
                }
    
                $data['reg_dokumen'] = $regulasiFile;
            } 
            // Jika tidak ada upload file baru tetapi tipe dokumen berubah dari link ke file
            elseif ($data['reg_tipe_dokumen'] == 'file' && $regulasi->reg_tipe_dokumen == 'link') {
                $data['reg_dokumen'] = $regulasi->reg_dokumen;
            }
    
            $regulasi->update($data);
    
            TransactionModel::createData(
                'UPDATED',
                $regulasi->regulasi_id,
                $regulasi->reg_judul
            );
            $result = self::responFormatSukses($regulasi, 'Regulasi berhasil diperbarui');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($regulasiFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($regulasiFile);
            return self::responFormatError($e, 'Gagal memperbarui regulasi');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $regulasi = self::findOrFail($id);

            // Jika tipe dokumen adalah file, hapus file fisik
            if ($regulasi->reg_tipe_dokumen == 'file' && $regulasi->reg_dokumen) {
                self::removeFile($regulasi->reg_dokumen);
            }

            $regulasi->delete();

            TransactionModel::createData(
                'DELETED',
                $regulasi->regulasi_id,
                $regulasi->reg_judul
            );

            DB::commit();

            return self::responFormatSukses($regulasi, 'Regulasi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus regulasi');
        }
    }

    public static function detailData($id)
    {
        return self::with('KategoriRegulasi')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_regulasi.fk_t_kategori_regulasi' => 'required|exists:t_kategori_regulasi,kategori_reg_id',
            't_regulasi.reg_judul' => 'required|max:255',
            't_regulasi.reg_sinopsis' => 'required',
            't_regulasi.reg_tipe_dokumen' => 'required|in:file,link',
        ];

        // Validasi berbeda untuk file dan link
        if ($request->t_regulasi['reg_tipe_dokumen'] == 'file') {
            // Cek apakah ini adalah edit (ada id) atau create
            if ($request->route('id')) {
                $regulasi = self::findOrFail($request->route('id'));
                // Jika document sudah ada dan tidak mengupload baru
                if ($regulasi->reg_dokumen && !$request->hasFile('reg_dokumen_file')) {
                    // Tidak perlu validasi file
                } else {
                    $rules['reg_dokumen_file'] = 'required|file|mimes:pdf,doc,docx|max:5120';
                }
            } else {
                $rules['reg_dokumen_file'] = 'required|file|mimes:pdf,doc,docx|max:5120';
            }
        } else {
            $rules['t_regulasi.reg_dokumen'] = 'required|url';
        }

        $messages = [
            't_regulasi.fk_t_kategori_regulasi.required' => 'Kategori regulasi wajib dipilih',
            't_regulasi.fk_t_kategori_regulasi.exists' => 'Kategori regulasi tidak valid',
            't_regulasi.reg_judul.required' => 'Judul regulasi wajib diisi',
            't_regulasi.reg_judul.max' => 'Judul regulasi maksimal 255 karakter',
            't_regulasi.reg_sinopsis.required' => 'Sinopsis regulasi wajib diisi',
            't_regulasi.reg_tipe_dokumen.required' => 'Tipe dokumen wajib dipilih',
            't_regulasi.reg_tipe_dokumen.in' => 'Tipe dokumen harus file atau link',
            't_regulasi.reg_dokumen.required' => 'URL dokumen wajib diisi',
            't_regulasi.reg_dokumen.url' => 'Format URL dokumen tidak valid',
            'reg_dokumen_file.required' => 'File dokumen wajib diupload',
            'reg_dokumen_file.file' => 'File dokumen harus berupa file',
            'reg_dokumen_file.mimes' => 'File dokumen harus berformat pdf, doc, atau docx',
            'reg_dokumen_file.max' => 'Ukuran file dokumen maksimal 5 MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}