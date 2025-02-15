<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardMPUController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardMPU', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
