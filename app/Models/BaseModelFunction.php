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

    /**
     * Boot trait untuk mengatur event listeners
     */
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

    /**
     * Upload file ke storage dan mengembalikan nama file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $prefix Prefix untuk direktori penyimpanan
     * @return string Nama file yang disimpan
     */
    protected static function uploadFile($file, $prefix)
    {
        if (!$file) {
            return null;
        }

        $fileName = $prefix . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        return $fileName;
    }

    /**
     * Menghapus file dari storage
     *
     * @param string $fileName Nama file yang akan dihapus
     * @return void
     */
    protected static function removeFile($fileName)
    {
        if ($fileName) {
            $filePath = storage_path('app/public/' . $fileName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Format respons sukses untuk operasi CRUD dengan parameter dinamis
     *
     * @param mixed $data Data yang akan dikembalikan
     * @param string $message Pesan sukses
     * @param array $additionalParams Parameter tambahan yang ingin disertakan dalam respons
     * @return array
     */
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

    /**
     * Format respons error untuk kasus validasi gagal dengan parameter dinamis
     *
     * @param ValidationException $e Exception validasi
     * @param array $additionalParams Parameter tambahan yang ingin disertakan dalam respons
     * @return array
     */
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

    /**
     * Format respons error untuk exception umum dengan parameter dinamis
     *
     * @param \Exception $e Exception yang terjadi
     * @param string $prefix Awalan pesan error (misalnya: "Terjadi kesalahan saat...")
     * @param array $additionalParams Parameter tambahan yang ingin disertakan dalam respons
     * @return array
     */
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
