<?php

namespace App\Models\Website\LandingPage\KategoriAkses;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class DetailPintasanLainnyaModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_pintasan_lainnya';
    protected $primaryKey = 'detail_pintasan_lainnya_id';
    protected $fillable = [
        'fk_pintasan_lainnya',
        'dpl_judul',
        'dpl_url'
    ];
    // Relasi dengan Pintasan Lainnya
    public function pintasanLainnya()
    {
        return $this->belongsTo(PintasanLainnyaModel::class, 'fk_pintasan_lainnya', 'pintasan_lainnya_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    /**
     * Metode untuk mengambil data dengan optional filtering
     */
    public static function selectData()
    {
      
    }

    /**
     * Metode untuk membuat data baru
     */
    public static function createData()
    {

    }

    /**
     * Metode untuk mengupdate data
     */
    public static function updateData()
    {
       
    }

    /**
     * Metode untuk menghapus data (soft delete)
     */
    public static function deleteData()
    {
     
    }

    /**
     * Metode untuk validasi data
     */
    public static function validasiData()
    {
       
    }
}