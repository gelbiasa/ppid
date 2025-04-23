<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetailPintasanLainnyaModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_pintasan_lainnya';
    protected $primaryKey = 'detail_pintasan_lainnya_id';

    protected $fillable = [
        'fk_pintasan_lainnya',
        'dpl_judul',
        'dpl_url'
    ];

    // Relasi dengan Pintasan Lainnya
    public function pintasanLainnya()
    {
        return $this->belongsTo(PintasanLainnyaModel::class, 'fk_pintasan_lainnya', 'pintasan_lainnya_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    /**
     * Metode untuk mengambil data dengan optional filtering
     */
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('pintasanLainnya')
            ->where('isDeleted', 0);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('dpl_judul', 'like', "%{$search}%")
                  ->orWhereHas('pintasanLainnya', function ($sq) use ($search) {
                      $sq->where('tpl_nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        return self::paginateResults($query, $perPage);
    }


    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->t_detail_pintasan_lainnya;
            $detailPintasanLainnya = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $detailPintasanLainnya->detail_pintasan_lainnya_id,
                $detailPintasanLainnya->dpl_judul
            );

            DB::commit();
            return self::responFormatSukses($detailPintasanLainnya, 'Detail pintasan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat detail pintasan');
        }
    }

 
    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $detailPintasanLainnya = self::findOrFail($id);
            $data = $request->t_detail_pintasan_lainnya;
            $detailPintasanLainnya->update($data);

            TransactionModel::createData(
                'UPDATED',
                $detailPintasanLainnya->detail_pintasan_lainnya_id,
                $detailPintasanLainnya->dpl_judul
            );

            DB::commit();
            return self::responFormatSukses($detailPintasanLainnya, 'Detail pintasan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui detail pintasan');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $detailPintasanLainnya = self::findOrFail($id);
            $detailPintasanLainnya->delete();

            TransactionModel::createData(
                'DELETED',
                $detailPintasanLainnya->detail_pintasan_lainnya_id,
                $detailPintasanLainnya->dpl_judul
            );

            DB::commit();
            return self::responFormatSukses($detailPintasanLainnya, 'Detail pintasan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus detail pintasan');
        }
    }
    public static function detailData($id)
    {
        return self::with('pintasanLainnya')->findOrFail($id);
    }


    public static function validasiData($request)
    {
        $rules = [
            't_detail_pintasan_lainnya.fk_pintasan_lainnya' => 'required|exists:t_pintasan_lainnya,pintasan_lainnya_id',
            't_detail_pintasan_lainnya.dpl_judul' => 'required|max:100',
            't_detail_pintasan_lainnya.dpl_url' => 'required|url|max:100',
        ];

        $messages = [
            't_detail_pintasan_lainnya.fk_pintasan_lainnya.required' => 'Pintasan lainnya wajib dipilih',
            't_detail_pintasan_lainnya.fk_pintasan_lainnya.exists' => 'Pintasan lainnya tidak valid',
            't_detail_pintasan_lainnya.dpl_judul.required' => 'Judul wajib diisi',
            't_detail_pintasan_lainnya.dpl_judul.max' => 'Judul maksimal 100 karakter',
            't_detail_pintasan_lainnya.dpl_url.required' => 'URL wajib diisi',
            't_detail_pintasan_lainnya.dpl_url.url' => 'Format URL tidak valid',
            't_detail_pintasan_lainnya.dpl_url.max' => 'URL maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}