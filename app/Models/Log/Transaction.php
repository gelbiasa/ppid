<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_transaction';
    protected $primaryKey = 'log_transaction_id';
    protected $fillable = [
        'log_transaction_jenis',
        'log_transaction_aktivitas',
        'log_transaction_pelaku',
        'log_transaction_tanggal_aktivitas',
    ];
}
