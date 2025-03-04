<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_email';
    protected $primaryKey = 'log_email_id';
    protected $fillable = [
        'log_email_status',
        'log_email_nama_pengirim',
        'log_email_tujuan',
        'log_email_tanggal_dikirim'
    ];

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
