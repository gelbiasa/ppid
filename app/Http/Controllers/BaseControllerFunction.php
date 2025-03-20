<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

trait BaseControllerFunction
{
    /**
     * Mengembalikan respons sukses untuk redirect
     *
     * @param string $route Nama route untuk redirect
     * @param string $message Pesan sukses
     * @param array $additionalParams Parameter tambahan untuk with session
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectSuccess($route, $message = 'Data berhasil diproses', array $additionalParams = [])
    {
        $params = ['success' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect($route)->with($sessionParams);
    }
    
    /**
     * Mengembalikan respons error untuk redirect kembali
     *
     * @param string $message Pesan error
     * @param array $additionalParams Parameter tambahan untuk with session
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectError($message, array $additionalParams = [])
    {
        $params = ['error' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->with($sessionParams)->withInput();
    }
    
    /**
     * Mengembalikan respons error validasi untuk redirect kembali
     *
     * @param ValidationException $e Exception validasi
     * @param array $additionalParams Parameter tambahan untuk with session
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectValidationError(ValidationException $e, array $additionalParams = [])
    {
        $params = [];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->withErrors($e->validator)->with($sessionParams)->withInput();
    }
    
    /**
     * Mengembalikan respons error exception umum untuk redirect kembali
     *
     * @param \Exception $e Exception yang terjadi
     * @param string $prefix Awalan pesan error (misalnya: "Terjadi kesalahan saat...")
     * @param array $additionalParams Parameter tambahan untuk with session
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectException(\Exception $e, $prefix = 'Terjadi kesalahan', array $additionalParams = [])
    {
        $message = $prefix . ': ' . $e->getMessage();
        $params = ['error' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->with($sessionParams)->withInput();
    }

    /**
     * Membuat tombol-tombol action untuk tampilan data tabel
     *
     * @param string $baseUrl URL dasar untuk action
     * @param mixed $id ID data yang akan diproses
     * @param array $options Opsi tambahan untuk mengaktifkan/menonaktifkan tombol tertentu
     * @return string HTML untuk tombol-tombol action
     */
    protected function generateActionButtons($baseUrl, $id, array $options = [])
    {
        // Nilai default untuk opsi
        $defaultOptions = [
            'edit' => true,
            'detail' => true,
            'delete' => true,
            'editText' => 'Edit',
            'detailText' => 'Detail',
            'deleteText' => 'Hapus',
        ];
        
        // Menggabungkan opsi dari pengguna dengan default
        $options = array_merge($defaultOptions, $options);
        
        $actions = '';
        
        // Tombol Edit
        if ($options['edit']) {
            $actions .= '
                <button class="btn btn-sm btn-info" onclick="modalAction(\'' . url("$baseUrl/editData/{$id}") . '\')">
                    <i class="fas fa-edit"></i> ' . $options['editText'] . '
                </button>';
        }
        
        // Tombol Detail
        if ($options['detail']) {
            $actions .= '
                <button class="btn btn-sm btn-primary" onclick="modalAction(\'' . url("$baseUrl/detailData/{$id}") . '\')">
                    <i class="fas fa-eye"></i> ' . $options['detailText'] . '
                </button>';
        }
        
        // Tombol Delete
        if ($options['delete']) {
            $actions .= '
                <button class="btn btn-sm btn-danger" onclick="modalAction(\'' . url("$baseUrl/deleteData/{$id}") . '\')">
                    <i class="fas fa-trash"></i> ' . $options['deleteText'] . '
                </button>';
        }
        
        return $actions;
    }
    
    /**
     * Mengembalikan response JSON sukses
     *
     * @param mixed $data Data yang akan dikembalikan
     * @param string $message Pesan sukses
     * @param int $statusCode HTTP status code (default: 200)
     * @param array $additionalParams Parameter tambahan untuk response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonSuccess($data, $message = 'Data berhasil diproses', $statusCode = 200, array $additionalParams = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
    
    /**
     * Mengembalikan response JSON error validasi
     *
     * @param ValidationException $e Exception validasi
     * @param int $statusCode HTTP status code (default: 422)
     * @param array $additionalParams Parameter tambahan untuk response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonValidationError(ValidationException $e, $statusCode = 422, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
    
    /**
     * Mengembalikan response JSON error
     *
     * @param \Exception $e Exception yang terjadi
     * @param string $prefix Awalan pesan error (misalnya: "Terjadi kesalahan saat...")
     * @param int $statusCode HTTP status code (default: 500)
     * @param array $additionalParams Parameter tambahan untuk response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonError(\Exception $e, $prefix = 'Terjadi kesalahan', $statusCode = 500, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => $prefix . ': ' . $e->getMessage()
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
}