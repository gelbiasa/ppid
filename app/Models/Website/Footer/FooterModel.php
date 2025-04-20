<?php

namespace App\Models\Website\Footer;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FooterModel extends Model
{
    use TraitsModel;

    protected $table = 't_footer';
    protected $primaryKey = 'footer_id';
    protected $fillable = [
        'fk_m_kategori_footer',
        'f_judul_footer',
        'f_icon_footer',
        'f_url_footer',
    ];

    // Relasi dengan kategori footer
    public function kategoriFooter()
    {
        return $this->belongsTo(KategoriFooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::with('kategoriFooter')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('f_judul_footer', 'like', "%{$search}%")
                  ->orWhereHas('kategoriFooter', function ($subQuery) use ($search) {
                      $subQuery->where('kt_footer_nama', 'like', "%{$search}%");
                  });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $iconFile = self::uploadFile(
            $request->file('f_icon_footer'),
            'footer_icons'
        );
    
        try {
            DB::beginTransaction();
    
            $data = $request->t_footer;
            
            // Jika icon diupload
            if ($iconFile) {
                $data['f_icon_footer'] = $iconFile;
            }
    
            $footer = self::create($data);
    
            TransactionModel::createData(
                'CREATED',
                $footer->footer_id,
                $footer->f_judul_footer
            );
            $result = self::responFormatSukses($footer, 'Footer berhasil dibuat');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($iconFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($iconFile);
            return self::responFormatError($e, 'Gagal membuat footer');
        }
    }
    
    public static function updateData($request, $id)
    {
        $iconFile = self::uploadFile(
            $request->file('f_icon_footer'),
            'footer_icons'
        );
    
        try {
            DB::beginTransaction();
    
            $footer = self::findOrFail($id);
            $data = $request->t_footer;
    
            // Jika icon diupload
            if ($iconFile) {
                // Hapus icon lama jika ada
                if ($footer->f_icon_footer) {
                    self::removeFile($footer->f_icon_footer);
                }
    
                $data['f_icon_footer'] = $iconFile;
            }
    
            $footer->update($data);
    
            TransactionModel::createData(
                'UPDATED',
                $footer->footer_id,
                $footer->f_judul_footer
            );
            $result = self::responFormatSukses($footer, 'Footer berhasil diperbarui');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($iconFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($iconFile);
            return self::responFormatError($e, 'Gagal memperbarui footer');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $footer = self::findOrFail($id);

            // Hapus file icon jika ada
            if ($footer->f_icon_footer) {
                self::removeFile($footer->f_icon_footer);
            }

            $footer->delete();

            TransactionModel::createData(
                'DELETED',
                $footer->footer_id,
                $footer->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($footer, 'Footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus footer');
        }
    }

    public static function detailData($id)
    {
        return self::with('kategoriFooter')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_footer.fk_m_kategori_footer' => 'required|exists:m_kategori_footer,kategori_footer_id',
            't_footer.f_judul_footer' => 'required|max:100',
            't_footer.f_url_footer' => 'nullable|url|max:100',
        ];

        // Validasi icon
        if ($request->hasFile('f_icon_footer')) {
            $rules['f_icon_footer'] = [
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ];
        }

        $messages = [
            't_footer.fk_m_kategori_footer.required' => 'Kategori footer wajib dipilih',
            't_footer.fk_m_kategori_footer.exists' => 'Kategori footer tidak valid',
            't_footer.f_judul_footer.required' => 'Judul footer wajib diisi',
            't_footer.f_judul_footer.max' => 'Judul footer maksimal 100 karakter',
            't_footer.f_url_footer.url' => 'URL footer harus berupa URL yang valid',
            't_footer.f_url_footer.max' => 'URL footer maksimal 100 karakter',
            'f_icon_footer.image' => 'Ikon harus berupa gambar',
            'f_icon_footer.mimes' => 'Ikon hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            'f_icon_footer.max' => 'Ukuran ikon maksimal 2MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}