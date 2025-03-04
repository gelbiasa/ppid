<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardAdminController extends Controller
{
    use TraitsController;
    
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardADM', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
