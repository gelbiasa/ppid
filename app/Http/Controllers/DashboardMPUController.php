<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardMPUController extends Controller
{
    use TraitsController;
    
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardMPU', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
