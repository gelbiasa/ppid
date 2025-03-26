<?php

namespace App\Models\Website\Publikasi\Pengumuman;

use App\Models\Log\TransactionModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PengumumanDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_pengumuman_dinamis';
    protected $primaryKey = 'pengumuman_dinamis_id';
    protected $fillable = [
        'pd_nama_submenu',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    public static function getDataPengumumanLandingPage()
    {
        $arr_data = DB::table('t_pengumuman', 'tp')
            ->join('m_pengumuman_dinamis as mpd', 'tp.fk_m_pengumuman_dinamis', '=', 'mpd.pengumuman_dinamis_id')
            ->leftJoin('t_upload_pengumuman as tup', 'tp.pengumuman_id', '=', 'tup.fk_t_pengumuman')
            ->select([
                'tp.pengumuman_id',
                'tp.peg_judul',
                'tp.peg_slug',
                'tp.status_pengumuman',
                'mpd.pd_nama_submenu',
                'tup.up_thumbnail',
                'tup.up_type',
                'tup.up_value',
                'tup.up_konten',
                'tp.created_at'
            ])
            ->where('tp.isDeleted', 0)
            ->where('tp.status_pengumuman', 'aktif')
            ->where('mpd.isDeleted', 0)
            ->where('mpd.pd_nama_submenu', 'Pengumuman')
            ->whereIn('tup.up_type', ['file', 'konten']) // Menambahkan filter untuk tipe file dan konten saja
            ->orderBy('tp.created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($pengumuman) {
                 $deskripsi = strip_tags($pengumuman->up_konten);
                $paragraf = preg_split('/\n\s*\n/', $deskripsi)[0] ?? '';
                // Proses thumbnail
                $thumbnail = null;
                if ($pengumuman->up_thumbnail) {
                    $thumbnail = asset('storage/' . $pengumuman->up_thumbnail);
                }
                // Proses value 
                $value = $pengumuman->up_value;
                if ($pengumuman->up_type === 'file') {
                    $value = asset('storage/' . $pengumuman->up_value);
                }
                
                // Format tanggal 
                $formattedDate = \Carbon\Carbon::parse($pengumuman->created_at)->format('d F Y');
    
                return [
                    'kategoriSubmenu' => $pengumuman->pd_nama_submenu,
                    'id' => $pengumuman->pengumuman_id,
                    'judul' => $pengumuman->peg_judul,
                    'slug' => $pengumuman->peg_slug,
                    'kategoriSubmenu' => $pengumuman->pd_nama_submenu,
                    'thumbnail' => $thumbnail,
                    'tipe' => $pengumuman->up_type,
                    'value' => $value,
                    'deskripsi' => strlen($paragraf) > 200 
                    ? substr($paragraf, 0, 200) . '...' 
                    : $paragraf,
                    'url_selengkapnya' => url('#'),
                    'created_at' => $formattedDate
                ];
            })
            ->toArray();
        return $arr_data;
    }
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where('pd_nama_submenu', 'like', "%{$search}%");
        }

        // Tambahkan pengurutan
        $query->orderBy('pd_nama_submenu', 'asc');
        
        // Gunakan paginateResults dari trait BaseModelFunction
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_pengumuman_dinamis;
            $pengumumanDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $pengumumanDinamis->pengumuman_dinamis_id,
                $pengumumanDinamis->pd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat pengumuman dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $pengumumanDinamis = self::findOrFail($id);
            
            $data = $request->m_pengumuman_dinamis;
            $pengumumanDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $pengumumanDinamis->pengumuman_dinamis_id, 
                $pengumumanDinamis->pd_nama_submenu 
            );

            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui pengumuman dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            
            $pengumumanDinamis = self::findOrFail($id);
            
            $pengumumanDinamis->delete();

            TransactionModel::createData(
                'DELETED',
                $pengumumanDinamis->pengumuman_dinamis_id,
                $pengumumanDinamis->pd_nama_submenu
            );
                
            DB::commit();

            return self::responFormatSukses($pengumumanDinamis, 'Pengumuman dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus pengumuman dinamis');
        }
    }

    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_pengumuman_dinamis.pd_nama_submenu' => 'required|max:255',
        ];

        $messages = [
            'm_pengumuman_dinamis.pd_nama_submenu.required' => 'Nama submenu pengumuman wajib diisi',
            'm_pengumuman_dinamis.pd_nama_submenu.max' => 'Nama submenu pengumuman maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
    }