<?php

namespace App\Models\SistemInformasi\KategoriForm;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KategoriFormModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_form';
    protected $primaryKey = 'kategori_form_id';
    protected $fillable = [
        'kf_nama',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        return self::where('isDeleted', 0)->get();
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_kategori_form;
            $kategoriForm = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $kategoriForm->kategori_form_id,
                $kategoriForm->kf_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriForm, 'Kategori form berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat kategori form');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $kategoriForm = self::findOrFail($id);
            
            $data = $request->m_kategori_form;
            $kategoriForm->update($data);

            TransactionModel::createData(
                'UPDATED',
                $kategoriForm->kategori_form_id, 
                $kategoriForm->kf_nama 
            );

            DB::commit();

            return self::responFormatSukses($kategoriForm, 'Kategori form berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui kategori form');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $kategoriForm = self::findOrFail($id);
            
            $kategoriForm->delete();

            TransactionModel::createData(
                'DELETED',
                $kategoriForm->kategori_form_id,
                $kategoriForm->kf_nama
            );
                
            DB::commit();

            return self::responFormatSukses($kategoriForm, 'Kategori form berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus kategori form');
        }
    }

    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_kategori_form.kf_nama' => 'required|max:255',
        ];

        $messages = [
            'm_kategori_form.kf_nama.required' => 'Nama kategori form wajib diisi',
            'm_kategori_form.kf_nama.max' => 'Nama kategori form maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}