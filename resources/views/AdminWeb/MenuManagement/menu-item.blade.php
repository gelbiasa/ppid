<li class="dd-item" data-id="{{ $menu->web_menu_id }}">
    <div class="dd-handle">
        {{ $menu->wm_menu_nama }}
        <span class="float-right">
            <span class="badge {{ $menu->wm_status_menu == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->wm_status_menu }}
            </span>
            <button type="button" class="btn btn-xs btn-warning edit-menu" 
                    data-id="{{ $menu->web_menu_id }}"
                    data-toggle="modal" 
                    data-target="#editMenuModal">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-xs btn-danger delete-menu" 
                    data-id="{{ $menu->web_menu_id }}">
                <i class="fas fa-trash"></i>
            </button>
        </span>
    </div>
    @if($menu->children->count() > 0)
        <ol class="dd-list">
            @foreach($menu->children as $child)
                @include('adminweb.MenuManagement.menu-item', ['menu' => $child])
            @endforeach
        </ol>
    @endif
</li>
