{{-- resources/views/adminweb/MenuManagement/index.blade.php --}}
@php
    use App\Models\Website\WebMenuModel;
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
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'create')
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
                <strong>Catatan:</strong> Jika menu dipindahkan ke kategori menu berbeda, jenis menu akan otomatis
                menyesuaikan.
            </div>

            <div class="row">
                @php $counter = 0; @endphp
                @foreach($jenisMenuList as $kode => $nama)
                            @if($counter % 2 == 0 && $counter > 0)
                                </div>
                                <div class="row mt-4">
                            @endif

                            <div class="col-md-6">
                                <div class="card">
                                    @php
                                        // Tetapkan warna berbeda untuk setiap jenis menu
                                        $bgColors = [
                                            'SAR' => 'bg-dark',
                                            'ADM' => 'bg-primary',
                                            'MPU' => 'bg-success',
                                            'VFR' => 'bg-warning',
                                            'RPN' => 'bg-danger',
                                            'ADT' => 'bg-info'
                                        ];

                                        // Default color jika kode tidak ada di array
                                        $bgColor = $bgColors[$kode] ?? 'bg-secondary';

                                        // Tentukan warna teks berdasarkan latar belakang
                                        $textColor = ($kode == 'VFR') ? 'text-dark' : 'text-white';
                                    @endphp

                                    <div class="card-header {{ $bgColor }} {{ $textColor }}">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-fw 
                                                    @if($kode == 'SAR') fa-crown
                                                    @elseif($kode == 'ADM') fa-user-cog
                                                    @elseif($kode == 'MPU') fa-sitemap
                                                    @elseif($kode == 'VFR') fa-check-circle
                                                    @elseif($kode == 'RPN') fa-users
                                                    @elseif($kode == 'ADT') fa-search
                                                        @else fa-list
                                                    @endif
                                            "></i> {{ $nama }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="dd nestable-{{ $kode }}" data-jenis="{{ $kode }}">
                                            <ol class="dd-list">
                                                @foreach($menusByJenis[$kode]['menus'] as $menu)
                                                    @include('adminweb.MenuManagement.menu-item', ['menu' => $menu, 'kode' => $kode])
                                                @endforeach
                                            </ol>
                                        </div>
                                        @if(count($menusByJenis[$kode]['menus']) == 0)
                                            <div class="alert alert-warning">
                                                Belum ada menu untuk kategori {{ $nama }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @php $counter++; @endphp
                @endforeach
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
        HakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'create')
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
                                <label>Hak Akses<span class="text-danger">*</span></label>
                                <select class="form-control" name="web_menu[fk_m_level]" id="add_level_menu">
                                    <option value="">Pilih Hak Akses</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Hak Akses wajib diisi</div>
                            </div>
                            <div class="form-group">
                                <label>Kategori Menu</label>
                                <select class="form-control" name="web_menu[wm_parent_id]" id="add_parent_id">
                                    <option value="">-Set Sebagai Menu Utama</option>
                                    <!-- Menu parent akan difilter berdasarkan hak akses yang dipilih menggunakan JavaScript -->
                                </select>
                                <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis
                                    menyesuaikan dengan jenis menu induk.</small>
                            </div>
                            <div class="form-group">
                                <label>Nama Menu<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="add_menu_nama">
                                <div class="invalid-feedback">Nama menu wajib diisi</div>
                            </div>
                            <div class="form-group">
                                <label>URL Menu</label>
                                <select class="form-control" name="web_menu[fk_web_menu_url]" id="add_menu_url">
                                    <option value="">Pilih URL</option>
                                    <option value="">Null - Menu Utama dengan Sub Menu</option>
                                    @foreach($menuUrls as $url)
                                        @if($url->Application && $url->Application->app_key == 'app ppid')
                                            <option value="{{ $url->web_menu_url_id }}">
                                                {{ $url->wmu_nama }} | {{ $url->Application->app_nama }}
                                                <small>({{ $url->wmu_keterangan }})</small>
                                            </option>
                                        @endif
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

    <!-- Perbaikan di Form Modal Edit -->
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
                    <input type="hidden" name="web_menu[set_akses_menu_id]" id="edit_set_akses_menu_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Hak Akses<span class="text-danger">*</span></label>
                            <select class="form-control" name="web_menu[fk_m_level]" id="edit_level_menu">
                                <option value="">Pilih Hak Akses</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Hak Akses wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>Kategori Menu</label>
                            <select class="form-control" name="web_menu[wm_parent_id]" id="edit_parent_id">
                                <option value="">-Set Sebagai Menu Utama</option>
                                <!-- Menu parent akan diisi menggunakan JavaScript -->
                            </select>
                            <small class="form-text text-muted">Jika memilih kategori menu, jenis menu akan otomatis
                                menyesuaikan dengan jenis menu induk.</small>
                        </div>
                        <div class="form-group">
                            <label>Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="web_menu[wm_menu_nama]" id="edit_menu_nama">
                            <div class="invalid-feedback">Nama menu wajib diisi</div>
                        </div>
                        <div class="form-group">
                            <label>URL Menu</label>
                            <select class="form-control" name="web_menu[fk_web_menu_url]" id="edit_menu_url">
                                <option value="">Pilih URL</option>
                                <option value="">Null - Menu Utama dengan Sub Menu</option>
                                @foreach($menuUrls as $url)
                                    @if($url->Application && $url->Application->app_key == 'app ppid')
                                        <option value="{{ $url->web_menu_url_id }}">
                                            {{ $url->wmu_nama }} | {{ $url->Application->app_nama }}
                                            <small>({{ $url->wmu_keterangan }})</small>
                                        </option>
                                    @endif
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
                        <button type="submit" class="btn btn-primary" id="submitEditMenu">Simpan</button>
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
        .nestable-SAR .dd-handle {
            border-left: 5px solid #343a40;
            /* dark */
        }

        .nestable-ADM .dd-handle {
            border-left: 5px solid #007bff;
            /* primary */
        }

        .nestable-MPU .dd-handle {
            border-left: 5px solid #28a745;
            /* success */
        }

        .nestable-VFR .dd-handle {
            border-left: 5px solid #fd7e14;
            /* warning */
        }

        .nestable-RPN .dd-handle {
            border-left: 5px solid #dc3545;
            /* danger */
        }

        .nestable-ADT .dd-handle {
            border-left: 5px solid #17a2b8;
            /* info */
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
            // Simpan semua menu dalam variabel JavaScript untuk digunakan dalam filter
            const allMenus = @json($menus);

            // Daftar warna untuk setiap jenis menu
            const menuColors = {
                'SAR': 'border-left-dark',
                'ADM': 'border-left-primary',
                'MPU': 'border-left-success',
                'VFR': 'border-left-warning',
                'RPN': 'border-left-danger',
                'ADT': 'border-left-info'
            };

            // Simpan jenis menu asli untuk validasi
            let originalMenuLevel = '';

            // Initialize Nestable untuk setiap jenis menu
            $('.dd').each(function () {
                $(this).nestable({
                    maxDepth: 2,
                    group: 1 // Memungkinkan drag and drop antar kelompok
                });
            });

            // Tambahkan event listener untuk dropdown Hak Akses pada form tambah
            $('#add_level_menu').on('change', function () {
                let levelId = $(this).val();
                updateParentMenuOptions(levelId, $('#add_parent_id'));
            });

            // Tambahkan event listener untuk dropdown Hak Akses pada form edit
            $('#edit_level_menu').on('change', function () {
                let levelId = $(this).val();
                updateParentMenuOptions(levelId, $('#edit_parent_id'));
            });


            function updateParentMenuOptions(levelId, targetSelect) {
                // Reset dropdown
                targetSelect.empty().append('<option value="">-Set Sebagai Menu Utama</option>');

                // Dapatkan URL dinamis menggunakan fungsi yang sama dengan reorder
                const dynamicUrl = "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/get-parent-menus') }}";

                // Lakukan request AJAX untuk mendapatkan parent menu berdasarkan level
                $.ajax({
                    url: `${dynamicUrl}/${levelId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.parentMenus) {
                            response.parentMenus.forEach(function(menu) {
                                targetSelect.append(`
                                    <option value="${menu.web_menu_id}" data-level="${levelId}">
                                        ${menu.wm_menu_nama}
                                    </option>
                                `);
                            });
                        }
                    },
                    error: function() {
                        toastr.error('Gagal memuat menu induk');
                    }
                });
            }

            // Event listener untuk perubahan dropdown Hak Akses pada form tambah
            $('#add_jenis_menu').on('change', function () {
                let jenisMenu = $(this).val();
                updateParentMenuOptions(jenisMenu, $('#add_parent_id'));
            });

            // Event listener untuk perubahan dropdown Jenis Menu pada form edit
            $('#edit_jenis_menu').on('change', function () {
                let jenisMenu = $(this).val();
                updateParentMenuOptions(jenisMenu, $('#edit_parent_id'));
            });

            // Fungsi untuk mengumpulkan semua data menu dari semua kategori
            function collectAllMenuData() {
                let allData = [];

                // Kumpulkan data dari setiap nestable
                $('.dd').each(function () {
                    let jenisKode = $(this).data('jenis');
                    let levelData = $(this).nestable('serialize');

                    if (levelData && levelData.length > 0) {
                        // Tambahkan informasi level ke setiap item menu utama (tanpa parent)
                        levelData.forEach(item => {
                            // Hanya menambahkan level ke menu tanpa parent
                            if (!item.parent_id) {
                                item.level = jenisKode;
                            }

                            // Simpan informasi level kode untuk menu ini
                            item.level_kode = $('.dd-item[data-id="' + item.id + '"]').data('jenis');
                        });

                        allData = allData.concat(levelData);
                    }
                });

                return allData;
            }

            // Variable untuk mencegah multiple reload
            let reloadScheduled = false;

            // Handler ketika item dilepas (drop) setelah di-drag
            $('.dd').on('change', function () {
                // Dapatkan jenis kontainer tujuan
                let targetContainerJenis = $(this).data('jenis');
                let userLevelKode = '{{ Auth::user()->level->level_kode }}';

                // Jika container tujuan adalah SAR atau ada menu SAR yang di-drop ke container lain
                if (targetContainerJenis === 'SAR' && userLevelKode !== 'SAR') {
                    // Cek apakah ada item non-SAR yang dipindahkan ke container SAR
                    let nonSarItemInSarContainer = false;
                    $(this).find('.dd-item').each(function () {
                        if ($(this).data('jenis') !== 'SAR') {
                            nonSarItemInSarContainer = true;
                            return false; // break the loop
                        }
                    });

                    if (nonSarItemInSarContainer) {
                        // Reload halaman untuk membatalkan perubahan
                        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }
                }

                // Cek juga apakah item SAR dipindahkan ke container non-SAR
                if (targetContainerJenis !== 'SAR' && userLevelKode !== 'SAR') {
                    let sarItemInNonSarContainer = false;
                    $(this).find('.dd-item[data-jenis="SAR"]').each(function () {
                        sarItemInNonSarContainer = true;
                        return false; // break the loop
                    });

                    if (sarItemInNonSarContainer) {
                        // Reload halaman untuk membatalkan perubahan
                        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }
                }

                // Loop melalui semua item level 1 (menu utama) dalam kontainer ini
                $(this).find('> .dd-list > .dd-item').each(function () {
                    // Perbarui data-jenis untuk menu utama
                    $(this).attr('data-jenis', targetContainerJenis);

                    // Perbarui juga tampilan visual
                    updateMenuItemStyle($(this), targetContainerJenis);
                });
            });

            // Fungsi untuk memperbarui gaya tampilan menu sesuai jenisnya
            function updateMenuItemStyle(menuItem, jenis) {
                // Hapus kelas border warna lama
                menuItem.find('> .dd-handle').removeClass(function (index, className) {
                    return (className.match(/(^|\s)border-left-\S+/g) || []).join(' ');
                });

                // Tambahkan kelas border warna baru sesuai jenis
                const borderClass = menuColors[jenis] || 'border-left-secondary';
                menuItem.find('> .dd-handle').addClass(borderClass);
            }

            // Simpan Urutan Menu
            $('#saveOrderBtn').on('click', function () {
                let data = collectAllMenuData();
                let userLevelKode = '{{ Auth::user()->level->level_kode }}';

                // Validasi menu SAR
                if (userLevelKode !== 'SAR') {
                    // Dapatkan semua menu dengan jenis SAR
                    let sarMenus = [];
                    $('.dd-item[data-jenis="SAR"]').each(function () {
                        sarMenus.push({
                            id: parseInt($(this).data('id')),
                            parent_id: $(this).parent().closest('.dd-item').data('id') || null
                        });
                    });

                    // Cek apakah ada perubahan pada menu SAR
                    let hasSARChange = false;
                    for (let sarMenu of sarMenus) {
                        // Cari menu SAR di data yang akan disimpan
                        let foundInData = data.find(item => item.id === sarMenu.id);

                        // Jika tidak ditemukan di level 1, periksa anak-anak menu
                        if (!foundInData) {
                            for (let item of data) {
                                if (item.children) {
                                    foundInData = item.children.find(child => child.id === sarMenu.id);
                                    if (foundInData) {
                                        // Jika ditemukan di anak, verifikasi parent ID
                                        if (foundInData.parent_id !== sarMenu.parent_id) {
                                            hasSARChange = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        } else if (foundInData.parent_id !== sarMenu.parent_id) {
                            // Jika ditemukan tapi parent ID berbeda
                            hasSARChange = true;
                        }

                        if (hasSARChange) break;
                    }

                    // Jika ada menu non-SAR yang dipindahkan ke container SAR
                    $('.dd[data-jenis="SAR"] .dd-item').each(function () {
                        if ($(this).data('jenis') !== 'SAR') {
                            hasSARChange = true;
                            return false; // break the loop
                        }
                    });

                    // Cek juga menu SAR yang dipindahkan ke container non-SAR
                    $('.dd:not([data-jenis="SAR"]) .dd-item').each(function () {
                        if ($(this).data('jenis') === 'SAR') {
                            hasSARChange = true;
                            return false; // break the loop
                        }
                    });

                    if (hasSARChange) {
                        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }
                }

                $.ajax({
                    url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/reorder') }}",
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
                            if (response.message.includes('SAR')) {
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        }
                    },
                    error: function () {
                        toastr.error('Error updating menu order');
                    }
                });
            });

            // Auto-update jenis menu saat memilih parent menu pada form tambah
            $('#add_parent_id').on('change', function () {
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
            $('#edit_parent_id').on('change', function () {
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

            // Validasi saat memilih jenis menu di form tambah
            $('#add_jenis_menu').on('change', function () {
                let selectedJenisMenu = $(this).val();
                if (selectedJenisMenu === 'SAR' && '{{ Auth::user()->level->level_kode }}' !== 'SAR') {
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat menambahkan menu SAR');
                    $(this).val('');
                }
            });

            // Validasi saat memilih jenis menu di form edit
            $('#edit_jenis_menu').on('change', function () {
                let selectedJenisMenu = $(this).val();
                if (selectedJenisMenu === 'SAR' && '{{ Auth::user()->level->level_kode }}' !== 'SAR') {
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                    $(this).val($(this).data('original-value') || '');
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
                    url: "/{{ WebMenuModel::getDynamicMenuUrl('menu-management') }}/" + menuId + "/detail_menu",
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            let menu = response.menu;

                            // Isi modal dengan data dari server
                            $('#detail_menu_nama').text(menu.wm_menu_nama || '-');
                            $('#detail_menu_url').text(menu.wm_menu_url || '-');
                            $('#detail_jenis_menu').text(menu.jenis_menu_nama || '-'); 
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
                HakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'update')
            )
                // Edit Menu
                $(document).on('click', '.edit-menu', function () {
                    let menuId = $(this).data('id');
                    let levelKode = $(this).data('level-kode');
                    
                    $('#edit_menu_id').val(menuId);

                    $.ajax({
                        url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/edit`,
                        type: 'GET',
                        success: function (response) {
                            if (response.success) {
                                // Set nilai form berdasarkan data dari response
                                $('#edit_menu_nama').val(response.menu.wm_menu_nama);
                                $('#edit_menu_url').val(response.menu.fk_web_menu_url);
                                $('#edit_status_menu').val(response.menu.wm_status_menu);
                                
                                // Set level menu
                                $('#edit_level_menu').val(response.menu.fk_m_level || '');
                                
                                // Perbarui dropdown Kategori Menu berdasarkan Level yang dipilih
                                updateParentMenuOptions(
                                    response.menu.fk_m_level, 
                                    $('#edit_parent_id')
                                );
                                
                                // Set selected parent - set setelah dropdown diupdate
                                setTimeout(function() {
                                    $('#edit_parent_id').val(response.menu.wm_parent_id);
                                }, 100);
                                
                                // Disable/enable level field based on parent
                                if (response.menu.wm_parent_id) {
                                    $('#edit_level_menu').prop('disabled', true);
                                } else {
                                    $('#edit_level_menu').prop('disabled', false);
                                }
                                
                                // Store original menu level for validation
                                originalMenuLevel = response.menu.level_kode;
                                
                                // Show edit modal
                                $('#editMenuModal').modal('show');
                            } else {
                                toastr.error(response.message || 'Gagal mengambil data menu');
                            }
                        },
                        error: function (xhr) {
                            console.error('Error fetching menu data:', xhr.responseText);
                            toastr.error('Error mengambil data menu');
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
                HakAksesModel::cekHakAkses(Auth::user()->user_id, WebMenuModel::getDynamicMenuUrl('menu-management'), 'delete')
            )
                // Delete Menu
                $(document).on('click', '.delete-menu', function () {
                    let menuId = $(this).data('id');
                    let menuName = $(this).data('name');
                    let levelKode = $(this).data('level-kode');

                    // Jika level menu adalah SAR dan pengguna bukan SAR, tolak
                    if (levelKode === 'SAR' && '{{ Auth::user()->level->level_kode }}' !== 'SAR') {
                        toastr.error('Hanya pengguna dengan level Super Administrator yang dapat menghapus menu SAR');
                        return false;
                    }

                    $('#confirmDelete').data('id', menuId);
                    $('#menuNameToDelete').text(menuName);
                    $('#deleteConfirmModal').modal('show');
                });

                // Event listener untuk konfirmasi hapus
                $('#confirmDelete').on('click', function () {
                    let menuId = $(this).data('id');

                    if (menuId) {
                        $.ajax({
                            url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/delete`,
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
                let levelMenu = $('#add_level_menu').val();
                let menuUrl = $('#add_menu_url').val();

                // Validasi Level Menu
                if (!levelMenu) {
                    $('#add_level_menu').addClass('is-invalid');
                    $('#add_level_menu').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                // Validasi Nama Menu
                if (!menuNama) {
                    $('#add_menu_nama').addClass('is-invalid');
                    $('#add_menu_nama').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                // Validasi URL Menu
                if (!menuUrl) {
                    $('#add_menu_url').addClass('is-invalid');
                    $('#add_menu_url').siblings('.invalid-feedback').text('URL menu wajib dipilih').show();
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
                    url: "{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management') . '/store') }}",
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
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                            });
                        } else {
                            toastr.error('Error creating menu');
                        }
                    }
                });
            });

            // Validasi Form Edit Menu - perbaikan untuk submit
            $('#editMenuForm').on('submit', function (e) {
                e.preventDefault();
                console.log('Edit form submitted');

                // Reset validasi
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                let isValid = true;
                let menuNama = $('#edit_menu_nama').val().trim();
                let statusMenu = $('#edit_status_menu').val();
                let levelMenu = $('#edit_level_menu').val();

                // Log nilai-nilai form untuk debugging
                console.log('Menu name:', menuNama);
                console.log('Status:', statusMenu);
                console.log('Level:', levelMenu);
                console.log('Parent ID:', $('#edit_parent_id').val());

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

                // Validasi Level Menu jika tidak ada parent
                if (!$('#edit_parent_id').val() && !levelMenu) {
                    $('#edit_level_menu').addClass('is-invalid');
                    $('#edit_level_menu').siblings('.invalid-feedback').show();
                    isValid = false;
                }

                if (!isValid) {
                    console.log('Form tidak valid');
                    return false;
                }

                // Validasi menu SAR
                if (originalMenuLevel === 'SAR' && '{{ Auth::user()->level->level_kode }}' !== 'SAR') {
                    toastr.error('Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR');
                    return false;
                }

                // Jika ada parent, pastikan level menu diambil dari parent
                if ($('#edit_parent_id').val()) {
                    // Aktifkan kembali level_menu agar dikirim dengan form
                    $('#edit_level_menu').prop('disabled', false);
                }

                let menuId = $('#edit_menu_id').val();
                console.log('Submitting form for menu ID:', menuId);
                
                $.ajax({
                    url: `{{ url('/' . WebMenuModel::getDynamicMenuUrl('menu-management')) }}/${menuId}/update`,
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
                        console.error('Error updating menu:', xhr.responseText);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                                // Tandai field yang error
                                $(`[name="${key}"]`).addClass('is-invalid');
                                $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]).show();
                            });
                        } else {
                            toastr.error('Error updating menu: ' + xhr.statusText);
                        }
                    }
                });
            });

            // Reset forms dan validasi setelah modal ditutup
            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                $('#edit_level_menu').prop('disabled', false);
                
                // Reset dropdown
                $('#edit_parent_id').empty().append('<option value="">-Set Sebagai Menu Utama</option>');
                
                // Reset variabel jenis menu asli
                originalMenuLevel = '';
            });
        });
    </script>
@endpush