<?php

namespace App\Models\SistemInformasi\KetentuanPelaporan;

use App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KetentuanPelaporanModel extends Model
{
    use TraitsModel;

    protected $table = 'm_ketentuan_pelaporan';
    protected $primaryKey = 'ketentuan_pelaporan_id';
    protected $fillable = [
        'fk_m_kategori_form',
        'kp_judul',
        'kp_konten',
    ];

    public function PelaporanKategoriForm()
    {
        return $this->belongsTo(KategoriFormModel::class, 'fk_m_kategori_form', 'kategori_form_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Fungsi untuk mengambil semua data dengan pagination
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('PelaporanKategoriForm')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kp_judul', 'like', "%{$search}%")
                    ->orWhereHas('PelaporanKategoriForm', function ($subq) use ($search) {
                        $subq->where('kf_nama', 'like', "%{$search}%");
                    });
            });
        }

        // Gunakan paginateResults dari trait BaseModelFunction
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_ketentuan_pelaporan;
            $ketentuan = self::create($data);

            DB::commit();

            return self::responFormatSukses($ketentuan, 'Ketentuan pelaporan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat ketentuan pelaporan');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $ketentuan = self::findOrFail($id);
            
            $data = $request->m_ketentuan_pelaporan;
            $ketentuan->update($data);

            DB::commit();

            return self::responFormatSukses($ketentuan, 'Ketentuan pelaporan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui ketentuan pelaporan');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $ketentuan = self::findOrFail($id);
            
            $ketentuan->delete();
                
            DB::commit();

            return self::responFormatSukses($ketentuan, 'Ketentuan pelaporan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus ketentuan pelaporan');
        }
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_ketentuan_pelaporan.fk_m_kategori_form' => 'required|exists:m_kategori_form,kategori_form_id',
            'm_ketentuan_pelaporan.kp_judul' => 'required|max:100',
            'm_ketentuan_pelaporan.kp_konten' => 'required',
        ];

        $messages = [
            'm_ketentuan_pelaporan.fk_m_kategori_form.required' => 'Kategori form wajib dipilih',
            'm_ketentuan_pelaporan.fk_m_kategori_form.exists' => 'Kategori form tidak valid',
            'm_ketentuan_pelaporan.kp_judul.required' => 'Judul ketentuan wajib diisi',
            'm_ketentuan_pelaporan.kp_judul.max' => 'Judul ketentuan maksimal 100 karakter',
            'm_ketentuan_pelaporan.kp_konten.required' => 'Konten ketentuan wajib diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}