<?php

namespace App\Helpers;

use App\Models\HakAkses\SetHakAksesModel;
use App\Models\Website\WebMenuModel;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    public static function renderSidebarMenus($hakAksesKode, $activeMenu)
    {
        $userId = Auth::user()->user_id;
        $menus = WebMenuModel::getMenusByLevelWithPermissions($hakAksesKode, $userId);
        $totalNotifikasi = WebMenuModel::getNotifikasiCount($hakAksesKode);

        $menuIcons = [
            'Dashboard' => 'fa-tachometer-alt',
            'Profile' => 'fa-user',
            'Notifikasi' => 'fa-bell',
            'Hak Akses' => 'fa-key',
            'E-Form' => 'fa-folder-open',
            'Menu Management' => 'fa-tasks'
        ];

        $html = '';

        // Dashboard dan Profil selalu ada
        $html .= self::generateMenuItem(
            url('/dashboard' . strtoupper($hakAksesKode)),
            'Dashboard',
            $menuIcons['Dashboard'],
            $activeMenu
        );

        $html .= self::generateMenuItem(
            url('/profile'),
            'Profile',
            $menuIcons['Profile'],
            $activeMenu
        );

        // Notifikasi untuk level tertentu
        if (in_array($hakAksesKode, ['ADM', 'VFR', 'MPU'])) {
            $notifUrl = [
                'ADM' => '/Notifikasi/NotifAdmin',
                'VFR' => '/notifikasi',
                'MPU' => '/notifMPU'
            ][$hakAksesKode];

            $html .= self::generateNotificationMenuItem(
                url($notifUrl),
                'Notifikasi',
                $menuIcons['Notifikasi'],
                $activeMenu,
                $totalNotifikasi
            );
        }

        // Menu dinamis dari database
        foreach ($menus as $menu) {
            // Ambil nama menu yang akan ditampilkan (bisa alias atau nama asli)
            $menuName = $menu->getDisplayName();

            if ($menu->children->isNotEmpty()) {
                // Menu dengan submenu
                $html .= self::generateDropdownMenu($menu, $activeMenu);
            } else {
                // Menu tanpa submenu - Gunakan URL yang sesuai
                $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : '#';
                $html .= self::generateMenuItem(
                    url($menuUrl),
                    $menuName,
                    $menuIcons[$menuName] ?? 'fa-tasks',
                    $activeMenu
                );
            }
        }

        // Menu khusus SAR
        if ($hakAksesKode == 'SAR') {
            $html .= self::generateMenuItem(
                url('/HakAkses'),
                'Pengaturan Hak Akses',
                $menuIcons['Hak Akses'],
                $activeMenu
            );
        }

        return $html;
    }

    private static function generateMenuItem($url, $name, $icon, $activeMenu)
    {
        $isActive = ($activeMenu == strtolower(str_replace(' ', '', $name))) ? 'active' : '';
        return "
        <li class='nav-item'>
            <a href='{$url}' class='nav-link {$isActive}'>
                <i class='nav-icon fas {$icon}'></i>
                <p>{$name}</p>
            </a>
        </li>";
    }

    private static function generateNotificationMenuItem($url, $name, $icon, $activeMenu, $notificationCount)
    {
        $isActive = ($activeMenu == 'notifikasi') ? 'active' : '';
        $notificationBadge = $notificationCount > 0
            ? "<span class='badge badge-danger notification-badge'>{$notificationCount}</span>"
            : '';

        return "
        <li class='nav-item'>
            <a href='{$url}' class='nav-link {$isActive}'>
                <i class='nav-icon fas {$icon}'></i>
                <p>{$name} {$notificationBadge}</p>
            </a>
        </li>";
    }

    private static function generateDropdownMenu($menu, $activeMenu)
    {
        // Ambil nama menu yang akan ditampilkan (bisa alias atau nama asli)
        $menuName = $menu->getDisplayName();

        $html = "
        <li class='nav-item'>
            <a href='#' class='nav-link'>
                <i class='nav-icon fas fa-cog'></i>
                <p>{$menuName}
                    <i class='right fas fa-angle-left'></i>
                </p>
            </a>
            <ul class='nav nav-treeview'>";

        foreach ($menu->children as $submenu) {
            // Ambil nama submenu yang akan ditampilkan (bisa alias atau nama asli)
            $submenuName = $submenu->getDisplayName();

            $submenuUrl = $submenu->WebMenuUrl ? $submenu->WebMenuUrl->wmu_nama : '#';
            $isActive = ($activeMenu == strtolower(str_replace(' ', '', $submenuName))) ? 'active' : '';
            
            $html .= "
                <li class='nav-item'>
                    <a href='" . url($submenuUrl) . "' class='nav-link {$isActive}'>
                        <i class='fas fa-tasks nav-icon'></i>
                        <p>{$submenuName}</p>
                    </a>
                </li>";
        }

        $html .= "
            </ul>
        </li>";

        return $html;
    }
}