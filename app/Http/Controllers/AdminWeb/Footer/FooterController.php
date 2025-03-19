<?php

namespace App\Http\Controllers\AdminWeb\Footer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\TraitsController;
use App\Models\Website\Footer\FooterModel;
use App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Validation\ValidationException;

class FooterController extends Controller
{
    use TraitsController;
    
//     // Halaman index footer
//     public function index()
//     {
//         $breadcrumb = (object) [
//             'title' => 'Manajemen Footer',
//             'list' => ['Home', 'Footer', 'Daftar']
//         ];
    
//         $page = (object) [
//             'title' => 'Daftar Footer'
//         ];
        
//         $activeMenu = 'footer';
//         $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
//         $footers = FooterModel::selectData(10);
    
//         return view('AdminWeb.Footer.index', [
//             'breadcrumb' => $breadcrumb,
//             'page' => $page,
//             'activeMenu' => $activeMenu,
//             'kategoriFooters' => $kategoriFooters,
//             'footers' => $footers
//         ]);
//     }

//     // Mendapatkan data untuk tabel
// public function getData(Request $request)
// {
//     $footers = FooterModel::selectData(10);
    
//     if ($request->ajax()) {
//         return view('AdminWeb.Footer.data', compact('footers'))->render();
//     }
    
//     return redirect()->route('footer.index');
// }
public function index(Request $request)
{
    $search = $request->query('search', '');

    $breadcrumb = (object) [
        'title' => 'Manajemen Footer',
        'list' => ['Home', 'Footer', 'Daftar']
    ];

    $page = (object) [
        'title' => 'Daftar Footer'
    ];
    
    $activeMenu = 'footer';
    $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
    
    // Modify the query to include search functionality
    $footers = FooterModel::selectData(10, $search);

    return view('AdminWeb.Footer.index', [
        'breadcrumb' => $breadcrumb,
        'page' => $page,
        'activeMenu' => $activeMenu,
        'kategoriFooters' => $kategoriFooters,
        'footers' => $footers,
        'search' => $search
    ]);
}

// Update getData method to support search
public function getData(Request $request)
{
    $search = $request->query('search', '');
    $footers = FooterModel::selectData(10, $search);
    
    if ($request->ajax()) {
        return view('AdminWeb.Footer.data', compact('footers', 'search'))->render();
    }
    
    return redirect()->route('footer.index');
}
  
    
    // Modal tambah footer
    public function addData()
    {
        $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
        return view('AdminWeb.Footer.create', compact('kategoriFooters'));
    }

    // Proses simpan footer
    public function createData(Request $request)
    {
        try {
            FooterModel::validasiData($request);
            $result = FooterModel::createData($request);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(FooterModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat membuat footer'));
        }
    }

    // Modal edit footer
    public function editData($id)
    {
        try {
            $footer = FooterModel::findOrFail($id);
            $kategoriFooters = KategoriFooterModel::where('isDeleted', 0)->get();
            
            return view('AdminWeb.Footer.update', [
                'footer' => $footer,
                'kategoriFooters' => $kategoriFooters
            ]);
        } catch (\Exception $e) {
            return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
        }
    }

    // Proses update footer
    public function updateData(Request $request, $id)
    {
        try {
            FooterModel::validasiData($request, $id);
            $result = FooterModel::updateData($request, $id);
            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json(FooterModel::responValidatorError($e));
        } catch (\Exception $e) {
            return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat memperbarui footer'));
        }
    }

    // Lihat detail footer
    public function detailData($id)
    {
        try {
            $footer = FooterModel::with('kategoriFooter')->findOrFail($id);
            
            return view('AdminWeb.Footer.detail', [
                'footer' => $footer,
                'title' => 'Detail Footer'
            ]);
        } catch (\Exception $e) {
            return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil detail'));
        }
    }

    // Halaman dan proses hapus footer
    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $footer = FooterModel::with('kategoriFooter')->findOrFail($id);
                
                return view('AdminWeb.Footer.delete', [
                    'footer' => $footer
                ]);
            } catch (\Exception $e) {
                return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat mengambil data'));
            }
        }
        
        try {
            $result = FooterModel::deleteData($id);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(FooterModel::responFormatError($e, 'Terjadi kesalahan saat menghapus footer'));
        }
    }
}