<?php

namespace App\Models\Website;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Log\TransactionModel;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebKontenModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'web_konten';
    protected $primaryKey = 'web_konten_id';

    protected $fillable = [
        'fk_web_menu',
        'wk_judul_konten',
        'wk_deskripsi_konten',
        'wk_status_konten'
    ];

    // Relationships
    public function WebMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'fk_web_menu', 'web_menu_id');
    }
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
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