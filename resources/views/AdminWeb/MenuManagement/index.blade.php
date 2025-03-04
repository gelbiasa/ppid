{{-- resources/views/adminweb/MenuManagement/index.blade.php --}}
@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                @if(Auth::user()->level->level_kode === 'SAR' || 
                    App\Models\HakAkses\HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'create'))
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addMenuModal">
                    <i class="fas fa-plus"></i> Tambah Menu
                </button>
                @endif
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
    @if(Auth::user()->level->level_kode === 'SAR' || 
    App\Models\HakAkses\HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'create'))
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
                            <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="add_menu_nama">
                            <div class="invalid-feedback">Nama menu wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>Kategori Menu</label>
                            <select class="form-control" name="web_menu[wm_parent_id]">
                                <option value="">-Set Sebagai Menu Utama</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="web_menu[wm_status_menu]" id="add_status_menu">
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                            <div class="invalid-feedback">Status menu wajib diisi</div>
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
    @endif

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
                    <input type="hidden" name="web_menu[web_menu_id]" id="edit_menu_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Menu Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="edit_menu_nama">
                            <div class="invalid-feedback">Nama menu wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>Kategori Menu</label>
                            <select class="form-control" name="web_menu[wm_parent_id]" id="edit_parent_id">
                                <option value="">-Set Sebagai Menu Utama</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="web_menu[wm_status_menu]" id="edit_status_menu">
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                            <div class="invalid-feedback">Status menu wajib diisi</div>
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
    <style>
        .dd-item-buttons {
        position: absolute;
        right: 10px;
        top: 7px;
        z-index: 10;
        }
        .dd-handle {
            padding-right: 120px !important; /* Provide space for buttons */
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 80%;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/nestable2/jquery.nestable.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

    <script>
        // Tambahkan token CSRF ke semua request AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
                        if (response.success) {
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
                    url: "/adminweb/menu-management/" + menuId + "/detail_menu",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log("Response dari server:", response); // Debugging

                        if (response.success) {
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
            @if(Auth::user()->level->level_kode === 'SAR' || 
               App\Models\HakAkses\HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'update'))
            $(document).on('click', '.edit-menu', function() {
                let menuId = $(this).data('id');
                $('#edit_menu_id').val(menuId);

                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
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
            @else
            $(document).on('click', '.edit-menu', function() {
                toastr.error('Anda tidak memiliki izin untuk mengubah menu');
                return false;
            });
            @endif

            // Hapus Menu
            @if(Auth::user()->level->level_kode === 'SAR' || 
               App\Models\HakAkses\HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'delete'))
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
                            if (response.success) {
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
            @else
            $(document).on('click', '.delete-menu', function() {
                toastr.error('Anda tidak memiliki izin untuk menghapus menu');
                return false;
            });
            @endif


            // Validasi Form Add Menu
            $('#addMenuForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset validasi
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                
                let isValid = true;
                let menuNama = $('#add_menu_nama').val().trim();
                let statusMenu = $('#add_status_menu').val();
                
                // Validasi Nama Menu
                if (!menuNama) {
                    $('#add_menu_nama').addClass('is-invalid');
                    $('#add_menu_nama').siblings('.invalid-feedback').show();
                    isValid = false;
                }
                
                // Validasi Status
                if (!statusMenu) {
                    $('#add_status_menu').addClass('is-invalid');
                    $('#add_status_menu').siblings('.invalid-feedback').show();
                    isValid = false;
                }
                
                if (!isValid) {
                    return false;
                }
                
                // Jika validasi berhasil, lanjutkan submit
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.success === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                                // Tandai field yang error
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                            });
                        } else {
                            toastr.error('Error creating menu');
                        }
                    }
                });
            });

            // Validasi Form Edit Menu
            $('#editMenuForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset validasi
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                
                let isValid = true;
                let menuNama = $('#edit_menu_nama').val().trim();
                let statusMenu = $('#edit_status_menu').val();
                
                // Validasi Nama Menu
                if (!menuNama) {
                    $('#edit_menu_nama').addClass('is-invalid');
                    $('#edit_menu_nama').siblings('.invalid-feedback').show();
                    isValid = false;
                }
                
                // Validasi Status
                if (!statusMenu) {
                    $('#edit_status_menu').addClass('is-invalid');
                    $('#edit_status_menu').siblings('.invalid-feedback').show();
                    isValid = false;
                }
                
                if (!isValid) {
                    return false;
                }
                
                let menuId = $('#edit_menu_id').val();
                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/update`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.success === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                                // Tandai field yang error
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                            });
                        } else {
                            toastr.error('Error updating menu');
                        }
                    }
                });
            });

            // Reset forms dan validasi setelah modal ditutup
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
            });
        });
    </script>
@endpush