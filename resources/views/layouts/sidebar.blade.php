<?php
use Illuminate\Support\Facades\Auth;
use App\Helpers\MenuHelper;

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
            {!! MenuHelper::renderSidebarMenus(Auth::user()->level->level_kode, $activeMenu) !!}
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