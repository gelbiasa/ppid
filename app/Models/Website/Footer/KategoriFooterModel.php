<?php

namespace App\Models\Website\Footer;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
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

    // Relasi dengan footer
    public function footer()
    {
        return $this->hasMany(FooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
    }

    // Metode untuk select data dengan pagination dan filter
    public static function selectData($request = null)
    {
        $query = self::where('isDeleted', 0);

        // Filter berdasarkan pencarian
        if ($request && $request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kt_footer_kode', 'like', '%' . $request->search . '%')
                    ->orWhere('kt_footer_nama', 'like', '%' . $request->search . '%');
            });
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

            // Cek apakah ada data yang sudah dihapus dengan kode/nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where(function ($query) use ($request) {
                    $query->where('kt_footer_kode', $request->kt_footer_kode)
                        ->orWhere('kt_footer_nama', $request->kt_footer_nama);
                })
                ->get();

            // Hapus data yang soft deleted dengan kode/nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'kt_footer_kode',
                'kt_footer_nama'
            ]);

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            $result = [
                'success' => true,
                'message' => 'Kategori Footer berhasil dibuat',
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
            Log::error('Error creating kategori footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat Kategori Footer: ' . $e->getMessage()
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

            // Cek apakah ada data yang sudah dihapus dengan kode/nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('kategori_footer_id', '!=', $id)
                ->where(function ($query) use ($request) {
                    $query->where('kt_footer_kode', $request->kt_footer_kode)
                        ->orWhere('kt_footer_nama', $request->kt_footer_nama);
                })
                ->get();

            // Hapus data yang soft deleted dengan kode/nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'kt_footer_kode',
                'kt_footer_nama'
            ]);

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            $result = [
                'success' => true,
                'message' => 'Kategori Footer berhasil diperbarui',
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
            Log::error('Error updating kategori footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui Kategori Footer: ' . $e->getMessage()
            ];
        }
    }

    // Metode hapus data (soft delete)
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            // Periksa apakah kategori sedang digunakan
            $footerCount = FooterModel::where('fk_m_kategori_footer', $id)
                ->where('isDeleted', 0)
                ->count();

            if ($footerCount > 0) {
                return [
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh footer'
                ];
            }

            DB::beginTransaction();

            // PERBAIKAN: Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            // Ini akan mengisi kolom deleted_at
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->kategori_footer_id,
                $saveData->kt_footer_nama
            );

            $result = [
                'success' => true,
                'message' => 'Kategori Footer berhasil dihapus',
                'data' => $saveData
            ];

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting kategori footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus Kategori Footer: ' . $e->getMessage()
            ];
        }
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            'kt_footer_kode' => [
                'required',
                'max:20',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan kode yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_kode', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Kode footer sudah digunakan');
                    }
                }
            ],
            'kt_footer_nama' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan nama yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_nama', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Nama footer sudah digunakan');
                    }
                }
            ],
        ];

        $messages = [
            'kt_footer_kode.required' => 'Kode footer wajib diisi',
            'kt_footer_kode.max' => 'Kode footer maksimal 20 karakter',
            'kt_footer_nama.required' => 'Nama footer wajib diisi',
            'kt_footer_nama.max' => 'Nama footer maksimal 100 karakter',
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
            $kategoriFooter = self::find($id);

            if (!$kategoriFooter) {
                return [
                    'success' => false,
                    'message' => 'Kategori Footer tidak ditemukan'
                ];
            }

            $result = [
                'success' => true,
                'kategori_footer' => [
                    'kt_footer_kode' => $kategoriFooter->kt_footer_kode,
                    'kt_footer_nama' => $kategoriFooter->kt_footer_nama,
                    'created_by' => $kategoriFooter->created_by,
                    'created_at' => $kategoriFooter->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $kategoriFooter->updated_by,
                    'updated_at' => $kategoriFooter->updated_at ? $kategoriFooter->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in detail kategori footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail Kategori Footer: ' . $e->getMessage()
            ];
        }
    }

    public static function getEditData($id)
    {
        try {
            $kategoriFooter = self::findOrFail($id);

            $result = [
                'success' => true,
                'kategori_footer' => $kategoriFooter
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error mengambil data edit kategori footer: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error mengambil data edit Kategori Footer: ' . $e->getMessage()
            ];
        }
    }
    public static function getDataTableList()
    {
        $query = self::select('kategori_footer_id', 'kt_footer_kode', 'kt_footer_nama');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '';
                $btn .= '<button onclick="showDetailKategoriFooter(' . $row->kategori_footer_id . ')" class="btn btn-info btn-sm" style="margin:2px" title="Detail"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\'' . url('/adminweb/kategori-footer/' . $row->kategori_footer_id . '/edit') . '\')" class="btn btn-warning btn-sm" style="margin:2px" title="Edit"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="deleteKategoriFooter(' . $row->kategori_footer_id . ')" class="btn btn-danger btn-sm" style="margin:2px" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}