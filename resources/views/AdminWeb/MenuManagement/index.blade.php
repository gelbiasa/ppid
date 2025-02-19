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
                    <h5 class="modal-title">Tambah Menu</h5>
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
                                <option value="">NULL (Menu Induk)</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="wm_status_menu" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
                                <option value="">NULL (Menu Induk)</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="wm_status_menu" id="edit_status_menu" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <strong id="menuNameToDelete"></strong>? Tindakan ini tidak dapat
                        dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Ya,Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/nestable2/jquery.nestable.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    <style>
        /* Container untuk seluruh menu */
        .card-body {
            max-height: 70vh;
            /* Tinggi maksimum 70% dari viewport height */
            overflow-y: auto;
            /* Membuat scroll vertikal */
            padding: 20px;
        }

        /* Membuat scrollbar lebih modern */
        .card-body::-webkit-scrollbar {
            width: 8px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .dd {
            width: 100% !important;
            max-width: none !important;
        }

        .dd-handle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 15px;
            min-height: 42px;
            flex-wrap: wrap;
            gap: 10px;
            background: #f8f9fa;
        }

        .dd-handle .float-right {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        /* Styling untuk text menu agar tidak terpotong */
        .dd-handle span.menu-text {
            flex: 1;
            min-width: 150px;
            word-break: break-word;
        }

        /* Memastikan tombol Simpan Urutan tetap terlihat */
        #saveOrderBtn {
            position: sticky;
            bottom: 20px;
            margin-top: 20px;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 576px) {
            .card-body {
                max-height: 80vh;
                /* Sedikit lebih tinggi untuk mobile */
            }

            .dd-handle {
                flex-direction: column;
                align-items: flex-start;
            }

            .dd-handle .float-right {
                width: 100%;
                justify-content: flex-end;
                margin-top: 5px;
            }

            .btn-xs {
                padding: 4px 8px;
            }
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/nestable2/jquery.nestable.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Nestable (tanpa auto-save)
            $('#nestable').nestable({
                maxDepth: 2
            });

            // Simpan Urutan Menu
            $('#saveOrderBtn').on('click', function() {
                let data = $('#nestable').nestable('serialize');
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
                            setTimeout(() => window.location.reload(),
                                1000); // Reload setelah 1 detik
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating menu order');
                    }
                });
            });

            // Edit Menu
            $(document).on('click', '.edit-menu', function() {
                let menuId = $(this).data('id');
                $('#edit_menu_id').val(menuId);

                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            $('#edit_menu_nama').val(response.menu.wm_menu_nama);
                            $('#edit_status_menu').val(response.menu.wm_status_menu);

                            let parentSelect = $('#edit_parent_id');
                            parentSelect.empty().append(
                                '<option value="">None (Top Level)</option>');
                            response.parentMenus.forEach(menu => {
                                parentSelect.append(
                                    `<option value="${menu.web_menu_id}">${menu.wm_menu_nama}</option>`
                                );
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
            $(document).ready(function() {
                // Event listener untuk menampilkan modal hapus dan menyimpan ID menu yang akan dihapus
                $(document).on('click', '.delete-menu', function() {
                    let menuId = $(this).data('id');
                    let menuName = $(this).data('name');

                    console.log("Menu ID yang akan dihapus:", menuId); // Debugging

                    $('#confirmDelete').data('id', menuId); // Simpan ID di tombol konfirmasi
                    $('#menuNameToDelete').text(menuName); // Tampilkan nama menu di modal
                    $('#deleteConfirmModal').modal('show');
                });

                // Event listener untuk konfirmasi hapus
                $('#confirmDelete').on('click', function() {
                    let menuId = $(this).data('id');

                    console.log("Menghapus menu ID:", menuId); // Debugging

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
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    toastr.error(response.message);
                                }
                                $('#deleteConfirmModal').modal('hide');
                            },
                            error: function(xhr) {
                                toastr.error('Error deleting menu');
                                console.error(xhr.responseText); // Debugging error
                                $('#deleteConfirmModal').modal('hide');
                            }
                        });
                    }
                });
            });

            // Simpan Perubahan Edit Menu
            $('#editMenuForm').on('submit', function(e) {
                e.preventDefault();
                let menuId = $('#edit_menu_id').val();
                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/update`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Error updating menu');
                        }
                    }
                });
            });

            // Tambah Menu Baru
            $('#addMenuForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Error creating menu');
                        }
                    }
                });
            });

            // Reset forms setelah modal ditutup
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>
@endpush
