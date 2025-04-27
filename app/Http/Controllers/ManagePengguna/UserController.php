<?php

namespace App\Http\Controllers\ManagePengguna;

use App\Http\Controllers\TraitsController;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan User';
    public $pagename = 'ManagePengguna/ManagementUser';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan User',
            'list' => ['Home', 'Pengaturan User']
        ];

        $page = (object) [
            'title' => 'Daftar Pengguna'
        ];

        $activeMenu = 'managementUser';

        // Gunakan pagination dan pencarian
        $level = UserModel::selectData(10, $search);

        return view("ManagePengguna/ManageUser.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'level' => $level,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pagination dan pencarian
    public function getData()
    {
        //
    }

    public function addData()
    {
        //
    }

    public function createData()
    {
       //
    }

    public function editData()
    {
        //
    }

    public function updateData()
    {
       //
    }

    public function detailData()
    {
        //
    }

    public function deleteData()
    {
        //
    }
}
