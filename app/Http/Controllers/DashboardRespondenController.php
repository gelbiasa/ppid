<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRespondenController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardResponden', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
