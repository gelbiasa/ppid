<li class="dd-item" data-id="{{ $menu->web_menu_id }}">
    <div class="dd-handle">
        <span class="menu-text">{{ $menu->wm_menu_nama }}</span>
        <span class="float-right">
            <span class="badge {{ $menu->wm_status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->wm_status_menu }}
            </span>
            <button type="button" class="btn btn-xs btn-warning edit-menu dd-nodrag" data-id="{{ $menu->web_menu_id }}"
                data-toggle="modal" data-target="#editMenuModal">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-xs btn-danger delete-menu dd-nodrag"
                data-id="{{ $menu->web_menu_id }}" data-name="{{ $menu->wm_menu_nama }}" data-toggle="modal"
                data-target="#deleteConfirmModal">
                <i class="fas fa-trash"></i>
            </button>
            <button type="button" class="btn btn-xs btn-info detail-menu dd-nodrag" data-id="{{ $menu->web_menu_id }}"
                data-toggle="modal" data-target="#detailMenuModal">
                <i class="fas fa-eye"></i>
            </button>
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
