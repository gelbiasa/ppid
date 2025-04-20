@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;

    // Ambil context dari parent view (jenis menu yang sedang dikelompokkan)
    $contextLevelKode = isset($kode) ? $kode : null;
    
    // Dapatkan level kode yang sesuai dengan menu ini
    $levelKode = $menu->Level ? $menu->Level->level_kode : '';
    $levelNama = $menu->Level ? $menu->Level->level_nama : 'Tidak Terdefinisi';

    // Tampilkan nama menu yang tepat berdasarkan hirarki data
    $displayName = $menu->getDisplayName();

    $dynamicMenuUrl = WebMenuModel::getDynamicMenuUrl('menu-management');
    $updateHakAkses = HakAksesModel::cekHakAkses(
        Auth::user()->user_id,
        $dynamicMenuUrl,
        'update'
    );
    $deleteHakAkses = HakAksesModel::cekHakAkses(
        Auth::user()->user_id,
        $dynamicMenuUrl,
        'delete'
    );

    $userLevelKode = Auth::user()->level->level_kode;
    
    // Kondisi untuk menampilkan tombol edit/delete
    $canEdit = ($userLevelKode === 'SAR') || ($updateHakAkses && $levelKode !== 'SAR');
    $canDelete = ($userLevelKode === 'SAR') || ($deleteHakAkses && $levelKode !== 'SAR');
@endphp

<li class="dd-item" data-id="{{ $menu->web_menu_id }}" data-level="{{ $menu->fk_m_level }}"
    data-jenis="{{ $levelKode }}">
    <div class="dd-handle">
        <!-- Gunakan variabel displayName yang sudah kita buat -->
        <span class="menu-text">{{ $displayName }}</span>
        <span class="float-right">
            <span class="badge {{ $menu->wm_status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->wm_status_menu }}
            </span>
            <span class="badge badge-info">
                {{ $levelNama }}
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
                    data-toggle="modal" data-target="#editMenuModal" data-level-kode="{{ $levelKode }}">
                    <i class="fas fa-edit"></i>
                </button>
            @endif

            <!-- Tombol Delete dengan kondisi yang sudah diperbaiki -->
            @if($canDelete)
                <button type="button" class="btn btn-xs btn-danger delete-menu dd-nodrag" 
                    data-id="{{ $menu->web_menu_id }}"
                    data-name="{{ $displayName }}" data-toggle="modal" data-target="#deleteConfirmModal"
                    data-level-kode="{{ $levelKode }}">
                    <i class="fas fa-trash"></i>
                </button>
            @endif
        </span>
    </div>
    @if ($menu->children->count() > 0)
        <ol class="dd-list">
            @foreach ($menu->children as $child)
                @include('adminweb.MenuManagement.menu-item', ['menu' => $child, 'kode' => $contextLevelKode])
            @endforeach
        </ol>
    @endif
</li>