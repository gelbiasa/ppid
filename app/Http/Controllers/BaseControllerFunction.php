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