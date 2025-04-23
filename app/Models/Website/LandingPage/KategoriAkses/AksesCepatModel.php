<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AksesCepatModel extends Model
{
    use TraitsModel;

    protected $table = 't_akses_cepat';
    protected $primaryKey = 'akses_cepat_id';

    // Kolom yang dapat diisi
    protected $fillable = [
        'fk_m_kategori_akses',
        'ac_judul',
        'ac_static_icon',
        'ac_animation_icon',
        'ac_url'
    ];
     // Relasi dengan Kategori Akses
     public function kategoriAkses()
     {
         return $this->belongsTo(KategoriAksesModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
     }


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
     public static function selectData($perPage = null, $search = '', $kategoriAksesId = null)
    {
        $query = self::with('kategoriAkses')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where('ac_judul', 'like', "%{$search}%");
        }

        // Filter berdasarkan kategori akses
        if ($kategoriAksesId !== null) {
            $query->where('fk_m_kategori_akses', $kategoriAksesId);
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
{
    $iconStatic = self::uploadFile(
        $request->file('t_akses_cepat.ac_static_icon'), 
        'akses_cepat_static_icons'
    );
    $iconAnim = self::uploadFile($request->file('t_akses_cepat.ac_animation_icon'), 'akses_cepat_animation_icons');

    try {
        DB::beginTransaction();

        $data = $request->t_akses_cepat;

        if ($iconStatic) {
            $data['ac_static_icon'] = $iconStatic;
        }
        if ($iconAnim) {
            $data['ac_animation_icon'] = $iconAnim;
        }

       $aksesCepat = self::create($data);

        TransactionModel::createData(
            'CREATED',
           $aksesCepat->akses_cepat_id,
           $aksesCepat->ac_judul
        );

        $result = self::responFormatSukses($aksesCepat, 'Data Akses Cepat berhasil dibuat');
        DB::commit(); 
        return $result;

    } catch (ValidationException $e) {
        DB::rollBack();
        self::removeFile($iconStatic);
        self::removeFile($iconAnim);
        return self::responValidatorError($e);
    } catch (\Exception $e) {
        DB::rollBack();
        self::removeFile($iconStatic);
        self::removeFile($iconAnim);
        return self::responFormatError($e, 'Gagal membuat Akses Cepat');
    }
}


public static function updateData($request, $id)
{
    $iconStatic = self::uploadFile(
        $request->file('t_akses_cepat.ac_static_icon'), 
        'akses_cepat_static_icons'
    );
    $iconAnim = self::uploadFile($request->file('t_akses_cepat.ac_animation_icon'), 'akses_cepat_animation_icons');

    try {
        DB::beginTransaction();

        $aksesCepat = self::findOrFail($id);
        $data = $request->t_akses_cepat;

        if ($iconStatic) {
            self::removeFile($aksesCepat->ac_static_icon);
            $data['ac_static_icon'] = $iconStatic;
        }

        if ($iconAnim) {
            self::removeFile($aksesCepat->ac_animation_icon);
            $data['ac_animation_icon'] = $iconAnim;
        }

       $aksesCepat->update($data);

        TransactionModel::createData('UPDATED',$aksesCepat->akses_cepat_id,$aksesCepat->ac_judul);

        $result = self::responFormatSukses($aksesCepat, 'Data Akses Cepat berhasil diperbarui');
        DB::commit(); 
        return $result;

    } catch (ValidationException $e) {
        DB::rollBack();
        self::removeFile($iconStatic);
        self::removeFile($iconAnim);
        return self::responValidatorError($e);
    } catch (\Exception $e) {
        DB::rollBack();
        self::removeFile($iconStatic);
        self::removeFile($iconAnim);
        return self::responFormatError($e, 'Gagal memperbarui Akses Cepat');
    }
}


    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

           $aksesCepat = self::findOrFail($id);

            // Hapus file icon jika ada
            self::removeFile($aksesCepat->ac_static_icon);
            self::removeFile($aksesCepat->ac_animation_icon);

           $aksesCepat->delete();

            TransactionModel::createData('DELETED',$aksesCepat->akses_cepat_id,$aksesCepat->ac_judul);

            DB::commit();
            return self::responFormatSukses($aksesCepat, 'Data Akses Cepat berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Akses Cepat');
        }
    }
    public static function detailData($id)
    {
        return self::with('kategoriAkses')->findOrFail($id);
    }
    public static function validasiData($request, $id = null)
    {
        // Aturan validasi dasar
        $rules = [
            't_akses_cepat.fk_m_kategori_akses' => 'required|exists:m_kategori_akses,kategori_akses_id',
            't_akses_cepat.ac_judul' => 'required|max:100',
            't_akses_cepat.ac_url' => 'required|url|max:100',
        ];

        // Jika create baru atau update dengan file baru
        if ($id === null) {
            // Untuk create baru
            $rules['t_akses_cepat.ac_static_icon'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            $rules['t_akses_cepat.ac_animation_icon'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2500';
        } else {
            // Untuk update
            if (request()->hasFile('t_akses_cepat.ac_static_icon')) {
                $rules['t_akses_cepat.ac_static_icon'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            }

            if (request()->hasFile('t_akses_cepat.ac_animation_icon')) {
                $rules['t_akses_cepat.ac_animation_icon'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            }
        }

        $messages = [
            't_akses_cepat.fk_m_kategori_akses.required' => 'Kategori akses wajib dipilih',
            't_akses_cepat.fk_m_kategori_akses.exists' => 'Kategori akses tidak valid',
            't_akses_cepat.ac_judul.required' => 'Judul akses cepat wajib diisi',
            't_akses_cepat.ac_judul.max' => 'Judul akses cepat maksimal 100 karakter',
            't_akses_cepat.ac_url.required' => 'URL akses cepat wajib diisi',
            't_akses_cepat.ac_url.url' => 'URL akses cepat harus berupa URL yang valid',
            't_akses_cepat.ac_url.max' => 'URL akses cepat maksimal 100 karakter',
            't_akses_cepat.ac_static_icon.required' => 'Ikon statis wajib diunggah',
            't_akses_cepat.ac_static_icon.image' => 'Ikon statis harus berupa gambar',
            't_akses_cepat.ac_static_icon.mimes' => 'Ikon statis hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            't_akses_cepat.ac_static_icon.max' => 'Ukuran ikon statis maksimal 2.5MB',
            't_akses_cepat.ac_animation_icon.image' => 'Ikon animasi harus berupa gambar',
            't_akses_cepat.ac_animation_icon.mimes' => 'Ikon animasi hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            't_akses_cepat.ac_animation_icon.max' => 'Ukuran ikon animasi maksimal 2.5MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}