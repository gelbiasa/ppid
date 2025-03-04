<?php
use Illuminate\Support\Facades\Auth;
use App\Models\Log\NotifAdminModel;
use App\Models\Log\NotifVerifikatorModel;

// Hitung total notifikasi belum dibaca
$totalNotifikasiVFR = NotifAdminModel::where('sudah_dibaca_notif_admin', null)->count();
$totalNotifikasiADM = NotifVerifikatorModel::where('sudah_dibaca_notif_verif', null)->count();
?>

<div class="sidebar">
    <!-- SidebarSearch Form -->
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
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Menu untuk setiap level_kode -->
            @if (Auth::user()->level->level_kode == 'ADM')
                <li class="nav-item">
                    <a href="{{ url('/dashboardADM') }}"
                        class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/notifAdmin') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                        @if ($totalNotifikasiADM > 0)
                            <span class="badge badge-danger notification-badge">{{ $totalNotifikasiADM }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p> Manage Pengguna
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/level') }}" class="nav-link {{ $activeMenu == 'level' ? 'active' : '' }} ">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Level User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/user') }}" class="nav-link {{ $activeMenu == 'user' ? 'active' : '' }}">
                                <i class="nav-icon far fa-user"></i>
                                <p>Data User</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">Website / Portal</li>
                <!-- Menu Utama -->
                @if(App\Helpers\MenuHelper::shouldShowInSidebar('adminweb/menu-management'))
                <li class="nav-item {{ request()->is('adminweb/menu-utama*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('adminweb/menu-utama*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Manajemen Menu
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/adminweb/menu-management') }}"
                                class="nav-link {{ $activeMenu == 'menumanagement' ? 'active' : '' }}">
                                <i class="fas fa-tasks nav-icon"></i>
                                <p>Menu Management</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="nav-header">Sistem Informasi</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p> E-Form
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PermohonanInformasi') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanInformasi' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Informasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PernyataanKeberatan') }}"
                                class="nav-link {{ $activeMenu == 'PernyataanKeberatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pernyataan Keberatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PengaduanMasyarakat') }}"
                                class="nav-link {{ $activeMenu == 'PengaduanMasyarakat' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaduan Masyarakat </p>
                            </a>
                        </li>
                    </ul>
                </li>
            @elseif (Auth::user()->level->level_kode == 'SAR')
                <li class="nav-item">
                    <a href="{{ url('/dashboardSAR') }}"
                        class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/HakAkses') }}" class="nav-link {{ $activeMenu == 'HakAkses' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>Hak Akses</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'MPU')
                <li class="nav-item">
                    <a href="{{ url('/dashboardMPU') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item" style="position: relative;">
                    <a href="{{ url('/notifMPU') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pengajuanPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'pengajuan_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Daftar Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pengajuanPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'pengajuan_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Daftar Pertanyaan</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'VFR')
                <li class="nav-item">
                    <a href="{{ url('/dashboardVFR') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item" style="position: relative;">
                    <a href="{{ url('/notifikasi') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                        @if ($totalNotifikasiVFR > 0)
                            <span class="badge badge-danger notification-badge">{{ $totalNotifikasiVFR }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/daftarPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'daftar_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Daftar Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/daftarPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'daftar_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Daftar Pertanyaan</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'RPN')
                <li class="nav-item">
                    <a href="{{ url('/dashboardRPN') }}"
                        class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p> E-Form
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PermohonanInformasi') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanInformasi' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Informasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PernyataanKeberatan') }}"
                                class="nav-link {{ $activeMenu == 'PernyataanKeberatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pernyataan Keberatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PengaduanMasyarakat') }}"
                                class="nav-link {{ $activeMenu == 'PengaduanMasyarakat' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaduan Masyarakat </p>
                            </a>
                        </li>
                    </ul>
                <li class="nav-item">
                    <a href="{{ url('/permohonan') }}" class="nav-link {{ $activeMenu == 'permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Pengajuan Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pertanyaan') }}" class="nav-link {{ $activeMenu == 'pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>Pengajuan Pertanyaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/hasilPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'hasil_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-scroll"></i>
                        <p>Hasil Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/hasilPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'hasil_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-scroll"></i>
                        <p>Hasil Pertanyaan</p>
                    </a>
                </li>

            @endif
        </ul>
    </nav>
</div>

<style>
    .notification-badge {
        position: absolute;
        top: 50%;
        /* Vertikal tengah */
        right: 10px;
        /* Geser ke kiri dari ujung kanan */
        transform: translateY(-50%);
        /* Perbaiki posisi tengah */
        background-color: #dc3545;
        /* Warna merah */
        color: white;
        /* Warna teks */
        padding: 3px 8px;
        /* Spasi dalam */
        border-radius: 12px;
        /* Membulatkan sudut */
        font-size: 12px;
        /* Ukuran font */
        font-weight: bold;
        /* Tebal */
    }
</style>