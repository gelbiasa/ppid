<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotifAdminModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_admin';
    protected $primaryKey = 'notif_admin_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_admin',
        'notif_admin_form_id',
        'pesan_notif_admin',
        'sudah_dibaca_notif_admin',
        'isDeleted',
        'created_at',
        'deleted_at'
    ];
}
