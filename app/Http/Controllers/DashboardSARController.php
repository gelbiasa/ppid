<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardSARController extends Controller
{
    use TraitsController;
    
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Super Administrator',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardSAR', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
