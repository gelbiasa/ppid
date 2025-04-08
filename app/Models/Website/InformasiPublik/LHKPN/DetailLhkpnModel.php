<?php

namespace App\Models\Website\InformasiPublik\LHKPN;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetailLhkpnModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_lhkpn';
    protected $primaryKey = 'detail_lhkpn_id';
    protected $fillable = [
        'fk_m_lhkpn',
        'dl_nama_karyawan',
        'dl_file_lhkpn'
    ];

    public function lhkpn()
    {
        return $this->belongsTo(LhkpnModel::class, 'fk_m_lhkpn', 'lhkpn_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::with('lhkpn')
            ->where('isDeleted', 0);
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('dl_nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('dl_file_lhkpn', 'like', "%{$search}%")
                    ->orWhereHas('lhkpn', function ($subQuery) use ($search) {
                        $subQuery->where('lhkpn_tahun', 'like', "%{$search}%");
                    });
            });
        }
    
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->t_detail_lhkpn;
            $detailLhkpn = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );

            DB::commit();

            return self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat detail LHKPN');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $detailLhkpn = self::findOrFail($id);
            $data = $request->t_detail_lhkpn;
            $detailLhkpn->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );

            DB::commit();

            return self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui detail LHKPN');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $detailLhkpn = self::findOrFail($id);

            $detailLhkpn->delete();

            TransactionModel::createData(
                'DELETED',
                $detailLhkpn->detail_lhkpn_id,
                $detailLhkpn->dl_nama_karyawan
            );

            DB::commit();

            return self::responFormatSukses($detailLhkpn, 'Detail LHKPN berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus detail LHKPN');
        }
    }

    public static function detailData($id)
    {
        return self::with('lhkpn')->findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            't_detail_lhkpn.fk_m_lhkpn' => 'required|exists:m_lhkpn,lhkpn_id',
            't_detail_lhkpn.dl_nama_karyawan' => 'required|max:100',
        ];

        // Validasi file
        if ($id) {
            // Update - file tidak wajib
            $rules['lhkpn_file'] = 'nullable|file|max:2560|mimes:pdf';
        } else {
            // Create - file wajib
            $rules['lhkpn_file'] = 'required|file|max:2560|mimes:pdf';
        }

        $messages = [
            't_detail_lhkpn.fk_m_lhkpn.required' => 'Tahun LHKPN wajib dipilih',
            't_detail_lhkpn.fk_m_lhkpn.exists' => 'LHKPN tidak valid',
            't_detail_lhkpn.dl_nama_karyawan.required' => 'Nama karyawan wajib diisi',
            't_detail_lhkpn.dl_nama_karyawan.max' => 'Nama karyawan maksimal 100 karakter',
            'lhkpn_file.required' => 'File LHKPN wajib diupload',
            'lhkpn_file.file' => 'Upload harus berupa file',
            'lhkpn_file.max' => 'Ukuran file maksimal 2.5MB',
            'lhkpn_file.mimes' => 'Format file harus PDF',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}