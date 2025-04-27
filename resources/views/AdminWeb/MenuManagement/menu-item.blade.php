@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;

    // Ambil context dari parent view (jenis menu yang sedang dikelompokkan)
    $contexthakAksesKode = isset($kode) ? $kode : null;
    
    // Dapatkan level kode yang sesuai dengan menu ini
    $hakAksesKode = $menu->Level ? $menu->Level->hak_akses_kode : '';
    $hakAksesNama = $menu->Level ? $menu->Level->hak_akses_nama : 'Tidak Terdefinisi';

    // Tampilkan nama menu yang tepat berdasarkan hirarki data
    $displayName = $menu->getDisplayName();

    $dynamicMenuUrl = WebMenuModel::getDynamicMenuUrl('menu-management');
    $updateHakAkses = SetHakAksesModel::cekHakAkses(
        Auth::user()->user_id,
        $dynamicMenuUrl,
        'update'
    );
    $deleteHakAkses = SetHakAksesModel::cekHakAkses(
        Auth::user()->user_id,
        $dynamicMenuUrl,
        'delete'
    );

    $userhakAksesKode = Auth::user()->level->hak_akses_kode;
    
    // Kondisi untuk menampilkan tombol edit/delete
    $canEdit = ($userhakAksesKode === 'SAR') || ($updateHakAkses && $hakAksesKode !== 'SAR');
    $canDelete = ($userhakAksesKode === 'SAR') || ($deleteHakAkses && $hakAksesKode !== 'SAR');
@endphp

<li class="dd-item" data-id="{{ $menu->web_menu_id }}" data-level="{{ $menu->fk_m_hak_akses }}"
    data-jenis="{{ $hakAksesKode }}">
    <div class="dd-handle">
        <!-- Gunakan variabel displayName yang sudah kita buat -->
        <span class="menu-text">{{ $displayName }}</span>
        <span class="float-right">
            <span class="badge {{ $menu->wm_status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->wm_status_menu }}
            </span>
            <span class="badge badge-info">
                {{ $hakAksesNama }}
            </span>

            <!-- Tombol Detail selalu tampil karena hanya untuk melihat -->
            <button type="button" class="btn btn-xs btn-info detail-menu dd-nodrag" 
                data-id="{{ $menu->web_menu_id }}"
                data-toggle="modal" data-target="#detailMenuModal">
                <i class="fas fa-eye"></i>
            </button>

            <!-- Tombol Edit dengan kondisi yang sudah diperbaiki -->
            @if($canEdit)
                <button type="button" class="btn btn-xs btn-warning edit-menu dd-nodrag" 
                    data-id="{{ $menu->web_menu_id }}"
                    data-toggle="modal" data-target="#editMenuModal" data-level-kode="{{ $hakAksesKode }}">
                    <i class="fas fa-edit"></i>
                </button>
            @endif

            <!-- Tombol Delete dengan kondisi yang sudah diperbaiki -->
            @if($canDelete)
                <button type="button" class="btn btn-xs btn-danger delete-menu dd-nodrag" 
                    data-id="{{ $menu->web_menu_id }}"
                    data-name="{{ $displayName }}" data-toggle="modal" data-target="#deleteConfirmModal"
                    data-level-kode="{{ $hakAksesKode }}">
                    <i class="fas fa-trash"></i>
                </button>
            @endif
        </span>
    </div>
    @if ($menu->children->count() > 0)
        <ol class="dd-list">
            @foreach ($menu->children as $child)
                @include('adminweb.MenuManagement.menu-item', ['menu' => $child, 'kode' => $contexthakAksesKode])
            @endforeach
        </ol>
    @endif
</li>