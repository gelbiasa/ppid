<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait BaseModelFunction
{
    protected $commonFields = [
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public static function bootBaseModelFunction()
    {
        // Event ketika model dibuat, isi created_by otomatis
        static::creating(function ($model) {
            if (!isset($model->created_by)) {
                if (session()->has('alias')) {
                    $model->created_by = session('alias');
                } else {
                    // Tambahkan default value untuk kasus registrasi
                    $model->created_by = 'System';
                }
            }
        });

        // Event ketika model diupdate, isi updated_by otomatis
        static::updating(function ($model) {
            if (session()->has('alias')) {
                $model->updated_by = session('alias');
            }

            // Pastikan updated_at diisi dengan timestamp sekarang
            $model->updated_at = now();
        });

        // Event ketika model dihapus (soft delete), isi deleted_by dan deleted_at otomatis
        static::deleting(function ($model) {
            if (session()->has('alias')) {
                $model->deleted_by = session('alias');
            } else {
                $model->deleted_by = 'System';
            }

            // Ubah kode ini dari conditional menjadi selalu mengisi
            $model->deleted_at = now();
        });
    }

    public function delete()
    {
        // Panggil event deleting yang akan mengisi deleted_by dan deleted_at
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // Update isDeleted jika belum diubah
        if ($this->isDeleted !== 1) {
            $this->isDeleted = 1;
        }

        // Pastikan deleted_at diisi dengan timestamp sekarang
        // Tambahan ini memastikan field deleted_at selalu terisi
        $this->deleted_at = now();

        // Simpan perubahan
        $this->save();

        // Fire event deleted
        $this->fireModelEvent('deleted');

        return true;
    }

    public function getCommonFields()
    {
        return $this->commonFields;
    }

    protected static function uploadFile($file, $prefix)
    {
        if (!$file) {
            return null;
        }

        $fileName = $prefix . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        return $fileName;
    }

    protected static function removeFile($fileName)
    {
        if ($fileName) {
            $filePath = storage_path('app/public/' . $fileName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    protected static function responFormatSukses($data, $message = 'Data berhasil diproses', array $additionalParams = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }

    protected static function responValidatorError(ValidationException $e, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }

    protected static function responFormatError(\Exception $e, $prefix = 'Terjadi kesalahan saat memproses data', array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => $prefix . ': ' . $e->getMessage()
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }
}
