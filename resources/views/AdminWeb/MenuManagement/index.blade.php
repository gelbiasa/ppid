{{-- resources/views/adminweb/MenuManagement/index.blade.php --}}
@php
use App\Models\HakAkses\HakAksesModel;
@endphp
@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'create')
                )
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addMenuModal">
                    <i class="fas fa-plus"></i> Tambah Menu
                </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Drag and drop item menu untuk menyusun ulang. <br>
                Setelah mengubah urutan, klik <strong>"Simpan Urutan"</strong> untuk menyimpan perubahan. <br>
                <strong>Catatan:</strong> Jika menu dipindahkan ke kategori menu berbeda, jenis menu akan otomatis menyesuaikan.
            </div>

            <div class="row">
                <!-- Baris 1, Kolom 1: Administrator -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-cog"></i> Administrator
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="dd nestable-ADM" data-jenis="ADM">
                                <ol class="dd-list">
                                    @foreach($menusByJenis['ADM']['menus'] as $menu)
                                        @include('adminweb.MenuManagement.menu-item', ['menu' => $menu])
                                    @endforeach
                                </ol>
                            </div>
                            @if(count($menusByJenis['ADM']['menus']) == 0)
                                <div class="alert alert-warning">
                                    Belum ada menu untuk kategori Administrator
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Baris 1, Kolom 2: Responden -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users"></i> Responden
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="dd nestable-RPN" data-jenis="RPN">
                                <ol class="dd-list">
                                    @foreach($menusByJenis['RPN']['menus'] as $menu)
                                        @include('adminweb.MenuManagement.menu-item', ['menu' => $menu])
                                    @endforeach
                                </ol>
                            </div>
                            @if(count($menusByJenis['RPN']['menus']) == 0)
                                <div class="alert alert-warning">
                                    Belum ada menu untuk kategori Responden
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Baris 2, Kolom 1: Manajemen dan Pimpinan Unit -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-sitemap"></i> Manajemen dan Pimpinan Unit
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="dd nestable-MPU" data-jenis="MPU">
                                <ol class="dd-list">
                                    @foreach($menusByJenis['MPU']['menus'] as $menu)
                                        @include('adminweb.MenuManagement.menu-item', ['menu' => $menu])
                                    @endforeach
                                </ol>
                            </div>
                            @if(count($menusByJenis['MPU']['menus']) == 0)
                                <div class="alert alert-warning">
                                    Belum ada menu untuk kategori Manajemen dan Pimpinan Unit
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Baris 2, Kolom 2: Verifikator -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-check-circle"></i> Verifikator
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="dd nestable-VFR" data-jenis="VFR">
                                <ol class="dd-list">
                                    @foreach($menusByJenis['VFR']['menus'] as $menu)
                                        @include('adminweb.MenuManagement.menu-item', ['menu' => $menu])
                                    @endforeach
                                </ol>
                            </div>
                            @if(count($menusByJenis['VFR']['menus']) == 0)
                                <div class="alert alert-warning">
                                    Belum ada menu untuk kategori Verifikator
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan Urutan (Drag and Drop) --}}
            <button type="button" class="btn btn-primary mt-3" id="saveOrderBtn">
                <i class="fas fa-save"></i> Simpan Urutan
            </button>
        </div>
    </div>

    <!-- Add Menu Modal -->
    @if(
        Auth::user()->level->level_kode === 'SAR' ||
        HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'create')
    )
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
                                <label>Jenis Menu<span class="text-danger">*</span></label>
                                <select class="form-control" name="web_menu[wm_jenis_menu]" id="add_jenis_menu">
                                    <option value="">Pilih Jenis Menu</option>
                                    <option value="ADM">Administrator</option>
                                    <option value="MPU">Manajemen dan Pimpinan Unit</option>
                                    <option value="VFR">Verifikator</option>
                                    <option value="RPN">Responden</option>
                                </select>
                                <div class="invalid-feedback">Jenis menu wajib diisi</div>
                            </div>
                            <div class="form-group">
                                <label>Kategori Menu</label>
                                <select class="form-control" name="web_menu[wm_parent_id]" id="add_parent_id">
                                    <option value="">-Set Sebagai Menu Utama</option>
                                    @foreach ($menus as $menu)
                                        <option value="{{ $menu->web_menu_id }}" data-jenis="{{ $menu->wm_jenis_menu }}">{{ $menu->wm_menu_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis menyesuaikan dengan jenis menu induk.</small>
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
                            <label>Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="edit_menu_nama">
                            <div class="invalid-feedback">Nama menu wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>Jenis Menu<span class="text-danger">*</span></label>
                            <select class="form-control" name="web_menu[wm_jenis_menu]" id="edit_jenis_menu">
                                <option value="">Pilih Jenis Menu</option>
                                <option value="ADM">Administrator</option>
                                <option value="MPU">Manajemen dan Pimpinan Unit</option>
                                <option value="VFR">Verifikator</option>
                                <option value="RPN">Responden</option>
                            </select>
                            <div class="invalid-feedback">Jenis menu wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>Kategori Menu</label>
                            <select class="form-control" name="web_menu[wm_parent_id]" id="edit_parent_id">
                                <option value="">-Set Sebagai Menu Utama</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->web_menu_id }}" data-jenis="{{ $menu->wm_jenis_menu }}">{{ $menu->wm_menu_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis menyesuaikan dengan jenis menu induk.</small>
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
        <div class="modal-dialog modal-md" role="document">
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
                            <th>Jenis Menu</th>
                            <td><span id="detail_jenis_menu"></span></td>
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
                    <button type="button" class="btn btn-danger" id="confirmDelete">Ya, Hapus</button>
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
            padding-right: 120px !important;
            /* Provide space for buttons */
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
        
        /* Warna berbeda untuk setiap jenis menu */
        .nestable-ADM .dd-handle {
            border-left: 5px solid #007bff;
        }
        .nestable-MPU .dd-handle {
            border-left: 5px solid #28a745;
        }
        .nestable-VFR .dd-handle {
            border-left: 5px solid #fd7e14;
        }
        .nestable-RPN .dd-handle {
            border-left: 5px solid #dc3545;
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

        $(document).ready(function () {
            // Initialize Nestable untuk setiap jenis menu
            $('.dd').each(function() {
                $(this).nestable({
                    maxDepth: 2,
                    group: 1 // Memungkinkan drag and drop antar kelompok
                });
            });

            // Fungsi untuk mengumpulkan semua data menu dari semua kategori
            function collectAllMenuData() {
                let allData = [];
                
                // Kumpulkan data dari setiap nestable
                $('.dd').each(function() {
                    let jenisMenu = $(this).data('jenis');
                    let jenisData = $(this).nestable('serialize');
                    
                    if (jenisData && jenisData.length > 0) {
                        // Tambahkan informasi jenis menu ke setiap item menu utama (tanpa parent)
                        jenisData.forEach(item => {
                            // Hanya menambahkan jenis ke menu tanpa parent
                            if (!item.parent_id) {
                                item.jenis = jenisMenu;
                            }
                        });
                        
                        allData = allData.concat(jenisData);
                    }
                });
                
                return allData;
            }

            // Handler ketika item dilepas (drop) setelah di-drag
            $('.dd').on('change', function() {
                // Dapatkan jenis kontainer tujuan
                let targetContainerJenis = $(this).data('jenis');
                
                // Loop melalui semua item level 1 (menu utama) dalam kontainer ini
                $(this).find('> .dd-list > .dd-item').each(function() {
                    // Perbarui data-jenis untuk menu utama
                    $(this).attr('data-jenis', targetContainerJenis);
                    
                    // Perbarui juga tampilan visual (opsional)
                    updateMenuItemStyle($(this), targetContainerJenis);
                });
            });
            
            // Fungsi untuk memperbarui gaya tampilan menu sesuai jenisnya
            function updateMenuItemStyle(menuItem, jenis) {
                // Hapus kelas border warna lama
                menuItem.find('> .dd-handle').removeClass(function(index, className) {
                    return (className.match(/(^|\s)border-left-\S+/g) || []).join(' ');
                });
                
                // Tambahkan kelas border warna baru sesuai jenis
                let borderClass = 'border-left-'; 
                switch(jenis) {
                    case 'ADM': borderClass += 'primary'; break;
                    case 'MPU': borderClass += 'success'; break;
                    case 'VFR': borderClass += 'warning'; break;
                    case 'RPN': borderClass += 'danger'; break;
                    default: borderClass += 'secondary';
                }
                
                menuItem.find('> .dd-handle').addClass(borderClass);
            }

            // Simpan Urutan Menu
            $('#saveOrderBtn').on('click', function () {
                let data = collectAllMenuData();
                
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/reorder') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: data
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Error updating menu order');
                    }
                });
            });

            // Auto-update jenis menu saat memilih parent menu pada form tambah
            $('#add_parent_id').on('change', function() {
                let parentId = $(this).val();
                if (parentId) {
                    // Ambil jenis menu dari data-jenis atribut di option yang dipilih
                    let jenisMenu = $(this).find('option:selected').data('jenis');
                    $('#add_jenis_menu').val(jenisMenu);
                    $('#add_jenis_menu').prop('disabled', true); // Disable field jenis menu
                } else {
                    $('#add_jenis_menu').prop('disabled', false); // Enable field jenis menu
                }
            });
            
            // Auto-update jenis menu saat memilih parent menu pada form edit
            $('#edit_parent_id').on('change', function() {
                let parentId = $(this).val();
                if (parentId) {
                    // Ambil jenis menu dari data-jenis atribut di option yang dipilih
                    let jenisMenu = $(this).find('option:selected').data('jenis');
                    $('#edit_jenis_menu').val(jenisMenu);
                    $('#edit_jenis_menu').prop('disabled', true); // Disable field jenis menu
                } else {
                    $('#edit_jenis_menu').prop('disabled', false); // Enable field jenis menu
                }
            });

            // Detail Menu
            $(document).on('click', '.detail-menu', function () {
                let menuId = $(this).data('id');

                if (!menuId) {
                    console.error("Data ID tidak ditemukan.");
                    return;
                }

                $.ajax({
                    url: "/adminweb/menu-management/" + menuId + "/detail_menu",
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            let menu = response.menu;

                            // Isi modal dengan data dari server
                            $('#detail_menu_nama').text(menu.wm_menu_nama || '-');
                            $('#detail_menu_url').text(menu.wm_menu_url || '-');
                            $('#detail_jenis_menu').text(menu.jenis_menu_nama || '-'); // Menampilkan nama jenis menu
                            $('#detail_parent_menu').text(
                                menu.wm_parent_id ?
                                    `Anak dari Menu ${menu.parent_menu_nama || '-'}` :
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
                    error: function (xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Terjadi kesalahan saat mengambil data menu.");
                    }
                });
            });

            // Edit Menu
            @if(
                Auth::user()->level->level_kode === 'SAR' ||
                HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'update')
            )
                $(document).on('click', '.edit-menu', function () {
                    let menuId = $(this).data('id');
                    $('#edit_menu_id').val(menuId);

                    $.ajax({
                        url: `{{ url('/adminweb/menu-management') }}/${menuId}/edit`,
                        type: 'GET',
                        success: function (response) {
                            if (response.success) {
                                $('#edit_menu_nama').val(response.menu.wm_menu_nama);
                                $('#edit_status_menu').val(response.menu.wm_status_menu);
                                $('#edit_jenis_menu').val(response.menu.wm_jenis_menu);
                                
                                // Reset parent dropdown
                                let parentSelect = $('#edit_parent_id');
                                parentSelect.empty().append('<option value="">-Set Sebagai Menu Utama</option>');
                                
                                // Populate parent dropdown with options
                                response.parentMenus.forEach(menu => {
                                    parentSelect.append(
                                        `<option value="${menu.web_menu_id}" data-jenis="${menu.wm_jenis_menu}">${menu.wm_menu_nama}</option>`
                                    );
                                });
                                
                                // Set selected parent
                                parentSelect.val(response.menu.wm_parent_id);
                                
                                // Disable jenis menu field if it has a parent
                                if (response.menu.wm_parent_id) {
                                    $('#edit_jenis_menu').prop('disabled', true);
                                } else {
                                    $('#edit_jenis_menu').prop('disabled', false);
                                }
                                
                                // Show edit modal
                                $('#editMenuModal').modal('show');
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            toastr.error('Error fetching menu details');
                        }
                    });
                });
            @else
                $(document).on('click', '.edit-menu', function () {
                    toastr.error('Anda tidak memiliki izin untuk mengubah menu');
                    return false;
                });
            @endif

            // Hapus Menu
            @if(
                Auth::user()->level->level_kode === 'SAR' ||
                HakAksesModel::cekHakAkses(Auth::user()->user_id, 'adminweb/menu-management', 'delete')
            )
                $(document).on('click', '.delete-menu', function () {
                    let menuId = $(this).data('id');
                    let menuName = $(this).data('name');

                    $('#confirmDelete').data('id', menuId); // Simpan ID di tombol konfirmasi
                    $('#menuNameToDelete').text(menuName); // Tampilkan nama menu di modal
                    $('#deleteConfirmModal').modal('show');
                });

                // Event listener untuk konfirmasi hapus
                $('#confirmDelete').on('click', function () {
                    let menuId = $(this).data('id');

                    if (menuId) {
                        $.ajax({
                            url: `{{ url('/adminweb/menu-management') }}/${menuId}/delete`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    toastr.error(response.message);
                                }
                                $('#deleteConfirmModal').modal('hide');
                            },
                            error: function (xhr) {
                                toastr.error('Error deleting menu');
                                console.error(xhr.responseText);
                                $('#deleteConfirmModal').modal('hide');
                            }
                        });
                    }
                });
            @else
                $(document).on('click', '.delete-menu', function () {
                    toastr.error('Anda tidak memiliki izin untuk menghapus menu');
                    return false;
                });
            @endif

            // Validasi Form Add Menu
            $('#addMenuForm').on('submit', function (e) {
                e.preventDefault();

                // Reset validasi
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                let isValid = true;
                let menuNama = $('#add_menu_nama').val().trim();
                let statusMenu = $('#add_status_menu').val();
                let jenisMenu = $('#add_jenis_menu').val();
                
                // Validasi Nama Menu
                if (!menuNama) {
                    $('#add_menu_nama').addClass('is-invalid');
                    $('#add_menu_nama').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                // Validasi Jenis Menu jika tidak ada parent
                if (!$('#add_parent_id').val() && !jenisMenu) {
                    $('#add_jenis_menu').addClass('is-invalid');
                    $('#add_jenis_menu').siblings('.invalid-feedback').show();
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
                
                // Jika ada parent, pastikan jenis menu diambil dari parent
                if ($('#add_parent_id').val()) {
                    let parentJenisMenu = $('#add_parent_id option:selected').data('jenis');
                    $('#add_jenis_menu').val(parentJenisMenu);
                    // Aktifkan kembali jenis menu agar dikirim dengan form
                    $('#add_jenis_menu').prop('disabled', false);
                }

                // Jika validasi berhasil, lanjutkan submit
                $.ajax({
                    url: "{{ url('/adminweb/menu-management/store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
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
            $('#editMenuForm').on('submit', function (e) {
                e.preventDefault();

                // Reset validasi
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                let isValid = true;
                let menuNama = $('#edit_menu_nama').val().trim();
                let statusMenu = $('#edit_status_menu').val();
                let jenisMenu = $('#edit_jenis_menu').val();
                
                // Validasi Nama Menu
                if (!menuNama) {
                    $('#edit_menu_nama').addClass('is-invalid');
                    $('#edit_menu_nama').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                // Validasi Jenis Menu jika tidak ada parent
                if (!$('#edit_parent_id').val() && !jenisMenu) {
                    $('#edit_jenis_menu').addClass('is-invalid');
                    $('#edit_jenis_menu').siblings('.invalid-feedback').show();
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
                
                // Jika ada parent, pastikan jenis menu diambil dari parent
                if ($('#edit_parent_id').val()) {
                    let parentJenisMenu = $('#edit_parent_id option:selected').data('jenis');
                    $('#edit_jenis_menu').val(parentJenisMenu);
                    // Aktifkan kembali jenis menu agar dikirim dengan form
                    $('#edit_jenis_menu').prop('disabled', false);
                }

                let menuId = $('#edit_menu_id').val();
                $.ajax({
                    url: `{{ url('/adminweb/menu-management') }}/${menuId}/update`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
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
            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                $('#add_jenis_menu, #edit_jenis_menu').prop('disabled', false);
            });
        });
    </script>
@endpush