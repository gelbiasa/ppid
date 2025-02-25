<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends EloquentModel
{
    use HasFactory, SoftDeletes;

    /**
     * Field yang umumnya ada di semua model
     */
    protected $commonFields = [
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    /**
     * Boot model dan tambahkan event hooks
     */
    protected static function boot()
    {
        parent::boot();

        // Event ketika model dibuat, isi created_by otomatis
        static::creating(function ($model) {
            if (!isset($model->created_by) && session()->has('alias')) {
                $model->created_by = session('alias');
            }
        });

        // Event ketika model diupdate, isi updated_by otomatis
        static::updating(function ($model) {
            if (session()->has('alias')) {
                $model->updated_by = session('alias');
            }
        });

        // Event ketika model dihapus (soft delete), isi deleted_by otomatis
        static::deleting(function ($model) {
            if (session()->has('alias')) {
                $model->deleted_by = session('alias');
            }
        });
    }

    /**
     * Mendapatkan semua field umum
     *
     * @return array
     */
    public function getCommonFields()
    {
        return $this->commonFields;
    }
}