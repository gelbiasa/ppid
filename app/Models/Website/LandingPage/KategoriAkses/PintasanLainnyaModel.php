<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class PintasanLainnyaModel extends Model
{
    use TraitsModel;

    protected $table = 't_pintasan_lainnya';
    protected $primaryKey = 'pintasan_lainnya_id';

    protected $fillable = [
        'fk_m_kategori_akses',
        'tpl_nama_kategori'
    ];


    public function kategoriAkses()
    {
        return $this->belongsTo(KategoriAksesModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
    }

    public function detailPintasanLainnya()
    {
        return $this->hasMany(DetailPintasanLainnyaModel::class, 'fk_pintasan_lainnya', 'pintasan_lainnya_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('kategoriAkses') 
            ->where('isDeleted', 0);
    
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('tpl_nama_kategori', 'like', "%{$search}%")
                  ->orWhereHas('kategoriAkses', function($sq) use ($search) {
                      $sq->where('mka_judul_kategori', 'like', "%{$search}%");
                  });
            });
        }
    
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->t_pintasan_lainnya;
            $pintasanLainnya = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $pintasanLainnya->pintasan_lainnya_id,
                $pintasanLainnya->tpl_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($pintasanLainnya, 'Pintasan lainnya berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat pintasan lainnya');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $pintasanLainnya = self::findOrFail($id);

            $data = $request->t_pintasan_lainnya;
            $pintasanLainnya->update($data);

            TransactionModel::createData(
                'UPDATED',
                $pintasanLainnya->pintasan_lainnya_id,
                $pintasanLainnya->tpl_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($pintasanLainnya, 'Pintasan lainnya berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui pintasan lainnya');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $pintasanLainnya = self::findOrFail($id);

            $pintasanLainnya->delete();

            TransactionModel::createData(
                'DELETED',
                $pintasanLainnya->pintasan_lainnya_id,
                $pintasanLainnya->tpl_nama_kategori
            );

            DB::commit();

            return self::responFormatSukses($pintasanLainnya, 'Pintasan lainnya berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus pintasan lainnya');
        }
    }

    public static function detailData($id)
    {
        return self::with('kategoriAkses')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_pintasan_lainnya.fk_m_kategori_akses' => 'required|exists:m_kategori_akses,kategori_akses_id',
            't_pintasan_lainnya.tpl_nama_kategori' => 'required|max:255',
        ];

        $messages = [
            't_pintasan_lainnya.fk_m_kategori_akses.required' => 'Kategori akses wajib dipilih',
            't_pintasan_lainnya.fk_m_kategori_akses.exists' => 'Kategori akses tidak valid',
            't_pintasan_lainnya.tpl_nama_kategori.required' => 'Nama kategori pintasan wajib diisi',
            't_pintasan_lainnya.tpl_nama_kategori.max' => 'Nama kategori pintasan maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}