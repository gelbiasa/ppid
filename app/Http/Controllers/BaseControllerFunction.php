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
}
