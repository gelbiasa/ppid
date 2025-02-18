{{-- resources/views/adminweb/MenuManagement/index.blade.php --}}
@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addMenuModal">
                    <i class="fas fa-plus"></i> Tambah Menu
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Drag and drop item menu untuk menyusun ulang. <br>
                Setelah mengubah urutan, klik <strong>"Simpan Urutan"</strong> untuk menyimpan perubahan.
            </div>

            <div class="dd" id="nestable">
                <ol class="dd-list">
                    @foreach ($menus as $menu)
                        @include('adminweb.MenuManagement.menu-item', ['menu' => $menu])
                    @endforeach
                </ol>
            </div>

            {{-- Tombol Simpan Urutan (Drag and Drop) --}}
            <button type="button" class="btn btn-primary mt-3" id="saveOrderBtn">
                <i class="fas fa-save"></i> Simpan Urutan
            </button>
        </div>
    </div>

    <!-- Add Menu Modal -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addMenuForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Menu Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="wm_menu_nama" required>
                        </div>
                        <div class="form-group">
                            <label>Parent Menu</label>
                            <select class="form-control" name="wm_parent_id">
                                <option value="">None (Top Level)</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="wm_status_menu" required>
                                <option value="aktif">Active</option>
                                <option value="nonaktif">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editMenuForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="menu_id" id="edit_menu_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Menu Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="wm_menu_nama" id="edit_menu_nama" required>
                        </div>
                        <div class="form-group">
                            <label>Parent Menu</label>
                            <select class="form-control" name="wm_parent_id" id="edit_parent_id">
                                <option value="">None (Top Level)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="wm_status_menu" id="edit_status_menu" required>
                                <option value="aktif">Active</option>
                                <option value="nonaktif">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this menu? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- Nestable CSS --}}
    <link href="{{ asset('assets/nestable/nestable.css') }}" rel="stylesheet">
    {{-- Toastr CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <style>
        .dd-handle {
            height: auto;
            min-height: 40px;
            padding: 8px 15px;
        }
        .dd-item > button {
            margin: 5px 0;
        }
        .badge {
            margin-right: 8px;
        }
    </style>
@endpush

@push('js')
    {{-- Nestable JS --}}
    <script src="{{ asset('assets/nestable/jquery.nestable.js') }}"></script>
    {{-- Toastr JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Nestable (tanpa auto-save)
            $('#nestable').nestable({
                maxDepth: 2
            });
            
            // Tombol "Simpan Urutan"
            $('#saveOrderBtn').on('click', function() {
                updateOrder();
            });

            // Fungsi Update Order
            function updateOrder() {
                var data = $('#nestable').nestable('serialize');
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/reorder') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: data
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating menu order');
                    }
                });
            }

            // Gunakan event delegation untuk tombol Edit
            $(document).on('click', '.edit-menu', function() {
                var menuId = $(this).data('id');
                $('#edit_menu_id').val(menuId);

                // Fetch menu data
                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            $('#edit_menu_nama').val(response.menu.wm_menu_nama);
                            $('#edit_status_menu').val(response.menu.wm_status_menu);

                            // Populate parent menu dropdown
                            var parentSelect = $('#edit_parent_id');
                            parentSelect.empty().append('<option value="">None (Top Level)</option>');
                            response.parentMenus.forEach(function(menu) {
                                parentSelect.append(`<option value="${menu.web_menu_id}">${menu.wm_menu_nama}</option>`);
                            });
                            parentSelect.val(response.menu.wm_parent_id);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching menu details');
                    }
                });
            });

            // Gunakan event delegation untuk tombol Delete
            $(document).on('click', '.delete-menu', function() {
                var menuId = $(this).data('id');
                $('#deleteConfirmModal').modal('show');
                // Simpan ID menu yang akan dihapus pada tombol konfirmasi
                $('#confirmDelete').data('id', menuId);
            });

            // Tombol konfirmasi Delete
            $('#confirmDelete').on('click', function() {
                var menuId = $(this).data('id');
                if (menuId) {
                    $.ajax({
                        url: `{{ url('/adminweb/menu-management') }}/${menuId}/delete`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message);
                            }
                            $('#deleteConfirmModal').modal('hide');
                        },
                        error: function() {
                            toastr.error('Error deleting menu');
                            $('#deleteConfirmModal').modal('hide');
                        }
                    });
                }
            });

            // Update Menu Form Submit
            $('#editMenuForm').on('submit', function(e) {
                e.preventDefault();
                var menuId = $('#edit_menu_id').val();
                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/update`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#editMenuModal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Error updating menu');
                        }
                    }
                });
            });

            // Add Menu Form Submit
            $('#addMenuForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#addMenuModal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Error creating menu');
                        }
                    }
                });
            });

            // Reset forms ketika modal ditutup
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>
@endpush
