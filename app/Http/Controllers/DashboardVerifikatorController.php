<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardVerifikatorController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('dashboardVFR', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
