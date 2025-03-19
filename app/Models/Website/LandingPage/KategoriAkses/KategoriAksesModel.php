<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class KategoriAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_akses';
    protected $primaryKey = 'Kategori_akses_id';
    protected $fillable = [
        'mka_judul_kategori'
    ];
    
    public function aksesCepat()
    {
        return $this->hasMany(AksesCepatModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
    }
    public function pintasanLainnya()
    {
        return $this->hasMany(PintasanLainnyaModel::class, 'fk_m_kategori_akses', 'Kategori_akses_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
  
    // Function API get data AksesCepat
public static function getDataAksesCepat()
{
    $arr_data = self::query()
        ->from('m_kategori_akses')
        ->select([
            'kategori_akses_id',
            'mka_judul_kategori'
        ])
        ->where('isDeleted', 0)
        ->where('mka_judul_kategori', 'Akses Menu Cepat')
        ->get()
        ->map(function ($kategori) {
            // Ambil Akses Cepat untuk kategori ini
            $aksesCepat = AksesCepatModel::query()
                ->select([
                    'akses_cepat_id',
                    'ac_judul',
                    'ac_static_icon',
                    'ac_animation_icon',
                    'ac_url'
                ])
                ->where('fk_m_kategori_akses', $kategori->kategori_akses_id)
                ->where('isDeleted', 0)
                ->get()
                ->map(function ($akses) {
                    return [
                        'id' => $akses->akses_cepat_id,
                        'judul' => $akses->ac_judul,
                        'static_icon' => $akses->ac_static_icon,
                        'animation_icon' => $akses->ac_animation_icon,
                        'url' => $akses->ac_url
                    ];
                });

            return [
                'kategori_id' => $kategori->kategori_akses_id,
                'kategori_judul' => $kategori->mka_judul_kategori,
                'akses_cepat' => $aksesCepat
            ];
        })
        ->toArray();

    return $arr_data;
}
    // Function API get data PintasanLainnya
    public static function getDataPintasanLainnya()
    {
        $arr_data = self::query()
            ->from('m_kategori_akses')
            ->select([
                'kategori_akses_id',
                'mka_judul_kategori'
            ])
            ->where('isDeleted', 0)
            ->where('mka_judul_kategori', 'Pintasan Lainnya')
            ->get()
            ->map(function ($kategori) {
                // Ambil Pintasan Lainnya untuk kategori ini
                $pintasanLainnya = PintasanLainnyaModel::query()
                    ->select([
                        'pintasan_lainnya_id',
                        'tpl_nama_kategori'
                    ])
                    ->where('fk_m_kategori_akses', $kategori->kategori_akses_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function ($pintasan) {
                        // Ambil detail untuk setiap Pintasan Lainnya
                        $details = DetailPintasanLainnyaModel::query()
                            ->select([
                                'detail_pintasan_lainnya_id',
                                'dpl_judul',
                                'dpl_url'
                            ])
                            ->where('fk_pintasan_lainnya', $pintasan->pintasan_lainnya_id)
                            ->where('isDeleted', 0)
                            ->get()
                            ->map(function ($detail) {
                                return [
                                    'id' => $detail->detail_pintasan_lainnya_id,
                                    'judul' => $detail->dpl_judul,
                                    'url' => $detail->dpl_url
                                ];
                            });

                        return [
                            'pintasan_id' => $pintasan->pintasan_lainnya_id,
                            'nama_kategori' => $pintasan->tpl_nama_kategori,
                            'detail' => $details
                        ];
                    });

                return [
                    'kategori_id' => $kategori->kategori_akses_id,
                    'kategori_judul' => $kategori->mka_judul_kategori,
                    'pintasan' => $pintasanLainnya
                ];
            })
            ->toArray();

        return $arr_data;
    }

    public static function selectData()
    {
        //
    }

    public static function createData()
    {
        //
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function validasiData()
    {
        //
    }
}