<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardRespondenController extends Controller
{
    use TraitsController;

    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardRPN', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
