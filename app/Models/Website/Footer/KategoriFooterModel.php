<?php

namespace App\Models\Website\Footer;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KategoriFooterModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_footer';
    protected $primaryKey = 'kategori_footer_id';
    protected $fillable = [
        'kt_footer_kode',
        'kt_footer_nama',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
     // Metode untuk select 
    public static function getDataFooter()
    {
        // Get all categories
        $categories = self::where('isDeleted', 0)
            ->select('kategori_footer_id', 'kt_footer_kode', 'kt_footer_nama')
            ->orderBy('kategori_footer_id')
            ->get();
    
        // Initialize result array
        $result = [];
    
        // For each category, get its footer items
        foreach ($categories as $category) {
            $footerItems = FooterModel::where('fk_m_kategori_footer', $category->kategori_footer_id)
                ->where('isDeleted', 0)
                ->select('footer_id', 'f_judul_footer', 'f_icon_footer', 'f_url_footer')
                ->orderBy('footer_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->footer_id,
                        'judul' => $item->f_judul_footer,
                        'icon' => $item->f_icon_footer ? asset('storage/footer_icons/' . $item->f_icon_footer) : null,
                        'url' => $item->f_url_footer
                    ];
                })->toArray();
    
            // Add category with its footer items to result
            $result[] = [
                'kategori_id' => $category->kategori_footer_id,
                'kategori_kode' => $category->kt_footer_kode,
                'kategori_nama' => $category->kt_footer_nama,
                'items' => $footerItems
            ];
        }
    
        return $result;
    }
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kt_footer_kode', 'like', "%{$search}%")
                  ->orWhere('kt_footer_nama', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_kategori_footer;
            $kategoriFooter = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat kategori footer');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $kategoriFooter = self::findOrFail($id);

            $data = $request->m_kategori_footer;
            $kategoriFooter->update($data);

            TransactionModel::createData(
                'UPDATED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui kategori footer');
        }
    }
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
    
            $kategoriFooter = self::findOrFail($id);
    
            // Periksa apakah ada footer yang terkait dengan kategori ini
            $footerTerkait = FooterModel::where('fk_m_kategori_footer', $id)
                ->where('isDeleted', 0)
                ->count();
    
            // Jika masih ada footer terkait, lempar exception
            if ($footerTerkait > 0) {
                throw new \Exception('Masih terdapat footer aktif yang terkait');
            }
    
            $kategoriFooter->delete();
    
            TransactionModel::createData(
                'DELETED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );
    
            DB::commit();
    
            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus kategori footer');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }
    
    public static function validasiData($request, $id = null)
    {
        $input = $request->m_kategori_footer;
        $rules = [];
        $messages = [];
    
        // Validasi kode footer
        if (isset($input['kt_footer_kode'])) {
            $uniqueRule = $id 
                ? 'unique:m_kategori_footer,kt_footer_kode,'.$id.',kategori_footer_id,isDeleted,0'
                : 'unique:m_kategori_footer,kt_footer_kode,NULL,kategori_footer_id,isDeleted,0';
    
            $rules['m_kategori_footer.kt_footer_kode'] = [
                'required', 
                'max:20', 
                $uniqueRule
            ];
    
            $messages['m_kategori_footer.kt_footer_kode.required'] = 'Kode footer wajib diisi';
            $messages['m_kategori_footer.kt_footer_kode.max'] = 'Kode footer maksimal 20 karakter';
            $messages['m_kategori_footer.kt_footer_kode.unique'] = 'Kode footer sudah digunakan';
        }
    
        // Validasi nama footer
        if (isset($input['kt_footer_nama'])) {
            $uniqueRule = $id 
                ? 'unique:m_kategori_footer,kt_footer_nama,'.$id.',kategori_footer_id,isDeleted,0'
                : 'unique:m_kategori_footer,kt_footer_nama,NULL,kategori_footer_id,isDeleted,0';
    
            $rules['m_kategori_footer.kt_footer_nama'] = [
                'required', 
                'max:100', 
                $uniqueRule
            ];
    
            $messages['m_kategori_footer.kt_footer_nama.required'] = 'Nama footer wajib diisi';
            $messages['m_kategori_footer.kt_footer_nama.max'] = 'Nama footer maksimal 100 karakter';
            $messages['m_kategori_footer.kt_footer_nama.unique'] = 'Nama footer sudah digunakan';
        }
    
        // Jika tidak ada field yang divalidasi, kembalikan true
        if (empty($rules)) {
            return true;
        }
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    
        return true;
    }
    // public static function validasiData($request)
    // {
    //     $rules = [
    //         'm_kategori_footer.kt_footer_kode' => 'required|max:20|unique:m_kategori_footer,kt_footer_kode,NULL,kategori_footer_id,isDeleted,0',
    //         'm_kategori_footer.kt_footer_nama' => 'required|max:100|unique:m_kategori_footer,kt_footer_nama,NULL,kategori_footer_id,isDeleted,0',
    //     ];

    //     $messages = [
    //         'm_kategori_footer.kt_footer_kode.required' => 'Kode footer wajib diisi',
    //         'm_kategori_footer.kt_footer_kode.max' => 'Kode footer maksimal 20 karakter',
    //         'm_kategori_footer.kt_footer_kode.unique' => 'Kode footer sudah digunakan',
    //         'm_kategori_footer.kt_footer_nama.required' => 'Nama footer wajib diisi',
    //         'm_kategori_footer.kt_footer_nama.max' => 'Nama footer maksimal 100 karakter',
    //         'm_kategori_footer.kt_footer_nama.unique' => 'Nama footer sudah digunakan',
    //     ];

    //     $validator = Validator::make($request->all(), $rules, $messages);

    //     if ($validator->fails()) {
    //         throw new ValidationException($validator);
    //     }

    //     return true;
    // }
}