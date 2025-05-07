<?php
use Illuminate\Support\Facades\Auth;
use App\Helpers\MenuHelper;
use Illuminate\Support\Facades\DB;

// Ambil user dan aktifkan hak akses
$user = Auth::user();
$userId = $user->user_id;

// Ambil hak akses aktif dari session
$activeHakAksesId = session('active_hak_akses_id');

// Jika tidak ada, cari yang pertama
if (!$activeHakAksesId) {
    $hakAkses = DB::table('set_user_hak_akses')
        ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
        ->where('set_user_hak_akses.fk_m_user', $userId)
        ->where('set_user_hak_akses.isDeleted', 0)
        ->where('m_hak_akses.isDeleted', 0)
        ->first();
        
    if ($hakAkses) {
        session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
        $activeHakAksesId = $hakAkses->hak_akses_id;
    }
}

// Ambil informasi hak akses
$hakAkses = DB::table('m_hak_akses')
    ->where('hak_akses_id', $activeHakAksesId)
    ->where('isDeleted', 0)
    ->first();
    
$hakAksesKode = $hakAkses ? $hakAkses->hak_akses_kode : '';
?>

<div class="sidebar">
    <div class="form-inline mt-2">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
    </div>
    
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            {!! MenuHelper::renderSidebarMenus($hakAksesKode, $activeMenu) !!}
        </ul>
    </nav>
</div>

<style>
    .notification-badge {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background-color: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
</style>