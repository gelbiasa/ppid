<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class SummernoteController extends Controller
{
    use TraitsController;

    public function getSummernoteJS()
    {
        $filePath = resource_path('js/summernote.js');
        
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        $content = File::get($filePath);
        
        // Pastikan file berisi definisi fungsi-fungsi yang dibutuhkan
        if (!str_contains($content, 'function aturSummernote') || 
            !str_contains($content, 'function aturFormModal')) {
            return response()->json(['error' => 'File JS tidak valid'], 500);
        }
        
        return response($content, 200)->header('Content-Type', 'application/javascript');
    }
    
    public function getSummernoteCSS()
    {
        $filePath = resource_path('css/summernote.css');
        
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        $content = File::get($filePath);
        return response($content, 200)->header('Content-Type', 'text/css');
    }
}