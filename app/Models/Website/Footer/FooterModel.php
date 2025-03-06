<?php

namespace App\Models\Website\Footer;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Log\TransactionModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
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

    // Metode untuk select data dengan pagination dan filter
    public static function selectData($request = null)
    {
        $query = self::with('kategoriFooter')->where('isDeleted', 0);

        // Filter berdasarkan pencarian
        if ($request && $request->has('search')) {
            $query->where('f_judul_footer', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request && $request->has('kategori')) {
            $query->where('fk_m_kategori_footer', $request->kategori);
        }

        // Sorting dan pagination
        return $query->paginate($request->perPage ?? 10);
    }

    // Metode create data
    public static function createData($request)
    {
        try {
            // Validasi input
            self::validasiData($request);

            DB::beginTransaction();

            // Persiapan data
            $data = $request->only([
                'fk_m_kategori_footer',
                'f_judul_footer',
                'f_url_footer'
            ]);

            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                $iconPath = $request->file('f_icon_footer')->store('footer_icons', 'public');
                $data['f_icon_footer'] = basename($iconPath);
            }
            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            $result = [
                'success' => true,
                'message' => 'Footer berhasil dibuat',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat Footer: ' . $e->getMessage()
            ];
        }
    }

    // Metode update data
    public static function updateData($request, $id)
    {
        try {
            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Persiapan data
            $data = $request->only([
                'fk_m_kategori_footer',
                'f_judul_footer',
                'f_url_footer'
            ]);

            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                // Hapus ikon lama jika ada
                if ($saveData->f_icon_footer) {
                    Storage::disk('public')->delete($saveData->f_icon_footer);
                }

                // Upload ikon baru
                $iconPath = $request->file('f_icon_footer')->store('footer_icons', 'public');
                $data['f_icon_footer'] = $iconPath;
            }

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            $result = [
                'success' => true,
                'message' => 'Footer berhasil diperbarui',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui Footer: ' . $e->getMessage()
            ];
        }
    }

    // Metode hapus data (soft delete)
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // PERBAIKAN: Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->save();

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            // Ini akan mengisi kolom deleted_at
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->footer_id,
                $saveData->f_judul_footer
            );

            $result = [
                'success' => true,
                'message' => 'Footer berhasil dihapus',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus Footer: ' . $e->getMessage()
            ];
        }
    }

    // Validasi data
// Validasi data
public static function validasiData($request, $id = null)
{
    $rules = [
        'fk_m_kategori_footer' => 'required|exists:m_kategori_footer,kategori_footer_id',
        'f_judul_footer' => 'required|max:100',
        'f_url_footer' => 'nullable|url|max:100', // Tetap nullable
        'f_icon_footer' => [
            'nullable', 
            'image', 
            'mimes:jpeg,png,jpg,gif,svg', // Tambahkan tipe file yang diizinkan
            'max:2048' // Tetap batasan ukuran 2MB
        ],
    ];

    $messages = [
        'fk_m_kategori_footer.required' => 'Kategori footer wajib dipilih',
        'fk_m_kategori_footer.exists' => 'Kategori footer tidak valid',
        'f_judul_footer.required' => 'Judul footer wajib diisi',
        'f_judul_footer.max' => 'Judul footer maksimal 100 karakter',
        'f_url_footer.url' => 'URL footer harus berupa URL yang valid',
        'f_url_footer.max' => 'URL footer maksimal 100 karakter',
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

    // Metode untuk mengambil detail data
    public static function getDetailData($id)
    {
        try {
            $footer = self::with('kategoriFooter')->find($id);

            if (!$footer) {
                return [
                    'success' => false,
                    'message' => 'Footer tidak ditemukan'
                ];
            }

            $result = [
                'success' => true,
                'footer' => [
                    'f_judul_footer' => $footer->f_judul_footer,
                    'f_url_footer' => $footer->f_url_footer,
                    'f_icon_footer' => $footer->f_icon_footer,
                    'kategori_footer' => $footer->kategoriFooter->kt_footer_nama,
                    'created_by' => $footer->created_by,
                    'created_at' => $footer->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $footer->updated_by,
                    'updated_at' => $footer->updated_at ? $footer->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in detail footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail Footer: ' . $e->getMessage()
            ];
        }
    }

    public static function getEditData($id)
    {
        try {
            $footer = self::findOrFail($id);

            $result = [
                'success' => true,
                'footer' => $footer
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error mengambil data edit footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error mengambil data edit Footer: ' . $e->getMessage()
            ];
        }
    }

    public static function getDataTableList()
    {
        $query = self::with('kategoriFooter')
            ->select('footer_id', 'fk_m_kategori_footer', 'f_judul_footer', 'f_url_footer', 'f_icon_footer');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kategori_footer', function ($row) {
                return $row->kategoriFooter ? $row->kategoriFooter->kt_footer_nama : '-';
            })
            ->addColumn('aksi', function ($row) {
                $btn = '';
                $btn .= '<button onclick="showDetailFooter(' . $row->footer_id . ')" class="btn btn-info btn-sm" style="margin:2px" title="Detail"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . url('/adminweb/footer/' . $row->footer_id . '/edit') . '\')" class="btn btn-warning btn-sm" style="margin:2px" title="Edit"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="deleteFooter(' . $row->footer_id . ')" class="btn btn-danger btn-sm" style="margin:2px" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

}