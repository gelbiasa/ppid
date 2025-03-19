<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;


class AksesCepatModel extends Model
{
    use TraitsModel;

    protected $table = 't_akses_cepat';
    protected $primaryKey = 'akses_cepat_id';

    // Kolom yang dapat diisi
    protected $fillable = [
        'ac_judul',
        'ac_static_icon', 'ac_animation_icon','ac_url'
    ];


    // Relasi dengan Kategori Akses
    public function kategoriAkses()
    {
        return $this->belongsTo(KategoriAksesModel::class, 'fk_m_kategori_akses', 'Kategori_akses_id');
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    
    
    /**
     * Metode untuk mengambil data dengan optional filtering
     */
    public static function selectData($request = null) {}

    /**
     * Metode untuk membuat data baru
     */
    public static function createData($request) {}

    /**
     * Metode untuk mengupdate data
     */
    public static function updateData($request, $id) {}

    /**
     * Metode untuk menghapus data (soft delete)
     */
    public static function deleteData($id) {}

    /**
     * Metode untuk validasi data
     */
    public static function validasiData($request) {}
}