<?php
namespace App\Http\Controllers\AdminWeb\Kontenweb;


use App\Http\Controllers\Controller;
use App\Models\Website\WebKontenModel;
use App\Models\Website\WebKontenImagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebKontenController extends Controller
{
    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebKontenModel::createData($request);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $result = WebKontenModel::updateData($request, $id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function delete($id)
    {
        if (request()->ajax()) {
            $result = WebKontenModel::deleteData($id);
            return response()->json($result);
        }
        return redirect()->back();
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/konten');
            return response()->json(['location' => Storage::url($path)]);
        }
        return response()->json(['error' => 'Gagal mengupload gambar'], 500);
    }

    public function deleteImage($id)
    {
        WebKontenImagesModel::deleteImage($id);
        return response()->json(['status' => true, 'message' => 'Gambar berhasil dihapus']);
    }
}