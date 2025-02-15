<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_level';
    protected $primaryKey = 'level_id';
    protected $fillable = [
        'level_kode',
        'level_nama',
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];
}
