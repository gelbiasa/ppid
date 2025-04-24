<?php

namespace App\Models\Website\Publikasi\Berita;


use Carbon\Carbon;

use App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Contracts\Providers\Storage;

class BeritaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_berita_dinamis';
    protected $primaryKey = 'berita_dinamis_id';
    protected $fillable = [
        'bd_nama_submenu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Relasi dengan Berita
    public function berita()
    {
        return $this->hasMany(BeritaModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
    }

    public static function getDataBeritaLandingPage()
    {
        $kategori = 1;
        $arr_data = DB::table('t_berita', 'tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.created_at',
                'tb.berita_thumbnail_deskripsi'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif')
            ->where('m_berita_dinamis.berita_dinamis_id', $kategori)
            ->orderBy('tb.created_at', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($berita) {
                $deskripsiThumbnail = trim($berita->berita_thumbnail_deskripsi);

                return [
                    'kategori' => $berita->bd_nama_submenu,
                    'judul' => $berita->berita_judul,
                    'slug' => $berita->berita_slug,
                    'deskripsiThumbnail' => strlen($deskripsiThumbnail) > 200
                        ? substr($deskripsiThumbnail, 0, 200) . '...'
                        : $deskripsiThumbnail,
                    'url_selengkapnya' => url('#')
                ];
            })
            ->toArray();

        return $arr_data;
    }
    // function get data berita 
    public static function getDataBerita($per_page = 5, $kategori_id = null)
    {
        $query = DB::table('t_berita as tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.created_at',
                'tb.berita_thumbnail_deskripsi',
                'tb.berita_thumbnail'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif');

        // Filter berdasarkan kategori jika ada
        if ($kategori_id) {
            $query->where('m_berita_dinamis.berita_dinamis_id', $kategori_id);
        }

        $arr_data = $query->orderBy('tb.berita_id', 'DESC')
            ->paginate($per_page);

        $transformedData = collect($arr_data->items())->map(function ($berita) {
            $deskripsiThumbnail = trim($berita->berita_thumbnail_deskripsi);
            $thumbnail = asset('storage/' . $berita->berita_thumbnail);
            $tanggal = Carbon::parse($berita->created_at)->format('d F Y');

            return [
                'berita_id' => $berita->berita_id,
                'kategori' => $berita->bd_nama_submenu,
                'judul' => $berita->berita_judul,
                'slug' => $berita->berita_slug,
                'thumbnail' => $thumbnail,
                'deskripsiThumbnail' => strlen($deskripsiThumbnail) > 200
                    ? substr($deskripsiThumbnail, 0, 200) . '...'
                    : $deskripsiThumbnail,
                'tanggal' => $tanggal,
                'url_selengkapnya' => url('berita/' . $berita->berita_slug)
            ];
        })->toArray();

        return [
            'current_page' => $arr_data->currentPage(),
            'data' => $transformedData,
            'total_pages' => $arr_data->lastPage(),
            'total_items' => $arr_data->total(),
            'per_page' => $arr_data->perPage(),
            'next_page_url' => $arr_data->nextPageUrl(),
            'prev_page_url' => $arr_data->previousPageUrl()
        ];
    }
    //  untuk API mengarahkan konten berita
    public static function getDataDetailBerita($slug)
    {
        $berita = DB::table('t_berita as tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_deskripsi'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.berita_slug', $slug)
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif')
            ->first();

        if (!$berita) {
            return null;
        }

        return [
            'berita_id' => $berita->berita_id,
            'judul' => $berita->berita_judul,
            'deskripsi' => $berita->berita_deskripsi // HTML dari summernote
        ];
    }

    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Add search functionality
        if (!empty($search)) {
            $query->where('bd_nama_submenu', 'like', "%{$search}%");
        }

          // Gunakan paginateResults dari trait BaseModelFunction
          return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_berita_dinamis;
            $kategoriBerita = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $kategoriBerita->berita_dinamis_id,
                $kategoriBerita->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($kategoriBerita, 'Kategori Sub Menu Berita Berhasil Dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal Membuat Kategori Sub Menu Berita');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $kategoriBerita = self::findOrFail($id);

            $data = $request->m_berita_dinamis;
            $kategoriBerita->update($data);

            TransactionModel::createData(
                'UPDATED',
                $kategoriBerita->berita_dinamis_id,
                $kategoriBerita->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($kategoriBerita, 'Kategori Sub Menu Berita berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Kategori Sub Menu Berita');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $kategoriBerita = self::findOrFail($id);

            $kategoriBerita->delete();

            TransactionModel::createData(
                'DELETED',
                $kategoriBerita->berita_dinamis_id,
                $kategoriBerita->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($kategoriBerita, 'Kategori Sub Menu Berita berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Kategori Sub Menu Berita');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_berita_dinamis.bd_nama_submenu' => 'required|max:255',
        ];

        $messages = [
            'm_berita_dinamis.bd_nama_submenu.required' => 'Nama Sub Menu Berita wajib diisi',
            'm_berita_dinamis.bd_nama_submenu.max' => 'Nama Sub Menu Berita maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}