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
                            <label>Nama Menu<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="wm_menu_nama" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Menu</label>
                            <select class="form-control" name="wm_parent_id">
                                <option value="">-Set Sebagai Menu Utama</option>
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
                            <label>Kategori Menu</label>
                            <select class="form-control" name="wm_parent_id" id="edit_parent_id">
                                <option value="">-Set Sebagai Menu Utama</option>
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
    <!-- Detail Menu Modal -->
    <div class="modal fade" id="detailMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document"> <!-- Ubah ke ukuran sedang -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detail Menu Aplikasi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Nama Menu</th>
                            <td><span id="detail_menu_nama"></span></td>
                        </tr>
                        <tr>
                            <th>URL Menu</th>
                            <td><span id="detail_menu_url"></span></td>
                        </tr>
                        <tr>
                            <th>Kategori Menu</th>
                            <td><span id="detail_parent_menu"></span></td>
                        </tr>
                        <tr>
                            <th>Urutan Menu</th>
                            <td><span id="detail_urutan_menu"></span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span id="detail_status_menu"></span></td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td><span id="detail_created_by"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td><span id="detail_created_at"></span></td>
                        </tr>
                        <tr>
                            <th>Diperbarui Oleh</th>
                            <td><span id="detail_updated_by"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal Diperbarui</th>
                            <td><span id="detail_updated_at"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
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
            // Detail Menu

            $(document).on('click', '.detail-menu', function() {
                let menuId = $(this).data('id');

                console.log("Memuat detail menu dengan ID:", menuId); // Debugging

                if (!menuId) {
                    console.error("Data ID tidak ditemukan.");
                    return;
                }

                $.ajax({
                    url: "/adminweb/menu-management/" + menuId +
                    "/detail_menu", // Gunakan URL yang sesuai
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log("Response dari server:", response); // Debugging

                        if (response.status) {
                            let menu = response.menu;

                            // Isi modal dengan data dari server
                            $('#detail_menu_nama').text(menu.wm_menu_nama || '-');
                            $('#detail_menu_url').text(menu.wm_menu_url || '-');
                            $('#detail_parent_menu').text(
                                menu.wm_parent_id ?
                                `Anak dari Menu ${menu.parent_menu_nama|| '-'}` :
                                'Menu Induk'
                            );
                            $('#detail_urutan_menu').text(menu.wm_urutan_menu || '-');
                            $('#detail_status_menu').html(
                                `<span class="badge ${menu.wm_status_menu === 'aktif' ? 'badge-success' : 'badge-danger'}">
                        ${menu.wm_status_menu}
                    </span>`
                            );
                            $('#detail_created_by').text(menu.created_by || '-');
                            $('#detail_created_at').text(menu.created_at || '-');
                            $('#detail_updated_by').text(menu.updated_by || '-');
                            $('#detail_updated_at').text(menu.updated_at || '-');

                            // Tampilkan modal setelah data terisi
                            $('#detailMenuModal').modal('show');

                        } else {
                            console.error("Gagal mendapatkan data:", response.message);
                            alert("Gagal memuat detail menu: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Terjadi kesalahan saat mengambil data menu.");
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
                                '<option value="">-Set Sebagai Menu Utama</option>');
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
