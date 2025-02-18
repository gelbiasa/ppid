<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotifVerifikatorModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_verifikator';
    protected $primaryKey = 'notif_verifikator_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_verif',
        'notif_verifikator_form_id',
        'pesan_notif_verif',
        'sudah_dibaca_notif_verif',
        'isDeleted',
        'created_at',
        'deleted_at'
    ];
}
