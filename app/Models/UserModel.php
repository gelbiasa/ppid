<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'fk_m_level', 
        'password', 
        'nama_pengguna', 
        'alamat_pengguna', 
        'no_hp_pengguna',
        'email_pengguna', 
        'pekerjaan_pengguna',
        'nik_pengguna',
        'upload_nik_pengguna',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $hidden = ['password']; // Tidak ditampilkan saat select
    protected $casts = ['password' => 'hashed']; // Password akan di-hash secara otomatis

    // Relasi ke tabel level
    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'fk_m_level', 'level_id');
    }

    public function getRoleName(): string 
    {
        return $this->level->level_nama;
    }
    public function hasRole($role): bool 
    {
        return $this->level->level_kode == $role;
    }

    /* Mendapatkan Kode Role */
    public function getRole()
    {
        return $this->level->level_kode;
    }
}
