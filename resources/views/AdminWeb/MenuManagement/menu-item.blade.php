{{-- resources/views/adminweb/MenuManagement/menu-item.blade.php --}}
@php
use App\Models\Website\WebMenuModel;
use App\Models\HakAkses\HakAksesModel;
@endphp

<li class="dd-item" data-id="{{ $menu->web_menu_id }}" data-jenis="{{ $menu->wm_jenis_menu }}">
    <div class="dd-handle">
        <span class="menu-text">{{ $menu->wm_menu_nama }}</span>
        <span class="float-right">
            <span class="badge {{ $menu->wm_status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->wm_status_menu }}
            </span>
            <span class="badge badge-info">
                @php
                    $jenisMenuList = WebMenuModel::getJenisMenuList();
                    echo $jenisMenuList[$menu->wm_jenis_menu] ?? $menu->wm_jenis_menu;
                @endphp
            </span>
            
            <!-- Tombol Detail selalu tampil karena hanya untuk melihat -->
            <button type="button" class="btn btn-xs btn-info detail-menu dd-nodrag" data-id="{{ $menu->web_menu_id }}"
                data-toggle="modal" data-target="#detailMenuModal">
                <i class="fas fa-eye"></i>
            </button>

            <!-- Tombol Edit hanya tampil jika user adalah SAR atau memiliki hak akses update -->
            @if(Auth::user()->level->level_kode === 'SAR' || 
                HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'update'))
            <button type="button" class="btn btn-xs btn-warning edit-menu dd-nodrag" data-id="{{ $menu->web_menu_id }}"
                data-toggle="modal" data-target="#editMenuModal">
                <i class="fas fa-edit"></i>
            </button>
            @endif
            
            <!-- Tombol Delete hanya tampil jika user adalah SAR atau memiliki hak akses delete -->
            @if(Auth::user()->level->level_kode === 'SAR' || 
                HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'delete'))
            <button type="button" class="btn btn-xs btn-danger delete-menu dd-nodrag"
                data-id="{{ $menu->web_menu_id }}" data-name="{{ $menu->wm_menu_nama }}" data-toggle="modal"
                data-target="#deleteConfirmModal">
                <i class="fas fa-trash"></i>
            </button>
            @endif
        </span>
    </div>
    @if ($menu->children->count() > 0)
        <ol class="dd-list">
            @foreach ($menu->children as $child)
                @include('adminweb.MenuManagement.menu-item', ['menu' => $child])
            @endforeach
        </ol>
    @endif
</li>