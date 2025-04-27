@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Hak Akses</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-all">
                    <i class="fas fa-save"></i> Simpan Semua Perubahan (Tombol Perubahan Pengaturan Hak Akses Per User)
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Sukses!</h5>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-info-circle"></i> Informasi!</h5>
                    {{ session('info') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-3">Pengaturan Hak Akses Berdasarkan Level</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($levelUsers as $hakAksesKode => $levelData)
                            <div class="col-md-4 mb-2">
                                <button class="btn btn-warning btn-block text-center set-hak-level"
                                    data-level="{{ $hakAksesKode }}" data-name="{{ $levelData['nama'] }}">
                                    <strong>{{ $levelData['nama'] }}</strong>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>


                <!-- Modal untuk Pengaturan Hak Akses Per Level -->
                <div class="modal fade" id="modalHakAksesLevel" tabindex="-1" aria-labelledby="modalTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitle">Pengaturan Hak Akses untuk <span
                                        id="levelTitle"></span>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="formHakAksesLevel">
                                    @csrf
                                    <input type="hidden" id="hakAksesKode" name="hak_akses_kode">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Menu Utama</th>
                                                <th>Sub Menu</th>
                                                <th class="text-center">Tampil Menu</th>
                                                <th class="text-center">Lihat</th>
                                                <th class="text-center">Tambah</th>
                                                <th class="text-center">Ubah</th>
                                                <th class="text-center">Hapus</th>
                                            </tr>
                                        </thead>
                                        <tbody id="menuList">
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" id="btnSimpanHakAksesLevel">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-footer">
                <h5 class="modal-title" id="modalTitle">Pengaturan Hak Akses Untuk Setiap User Berdasarkan Level
                    User<span id="levelTitle"></span>
                </h5>
            </div>

            <form action="{{ url('/HakAkses/updateData') }}" method="POST" id="form-hak-akses">
                @csrf
                <div class="accordion" id="accordionHakAkses">
                    @foreach($levelUsers as $hakAksesKode => $levelData)
                        <div class="card">
                            <div class="card-header" id="heading{{ $hakAksesKode }}">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                        data-target="#collapse{{ $hakAksesKode }}" aria-expanded="true"
                                        aria-controls="collapse{{ $hakAksesKode }}">
                                        <strong>{{ $levelData['nama'] }} ({{ $hakAksesKode }})</strong>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapse{{ $hakAksesKode }}" class="collapse" aria-labelledby="heading{{ $hakAksesKode }}"
                                data-parent="#accordionHakAkses">
                                <div class="card-body">
                                    @foreach($levelData['menus'] as $kategori => $submenus)
                                        <div class="menu-category mb-4">
                                            <h5>{{ $kategori }}</h5>
                                            <hr>

                                            @foreach($submenus as $submenuName => $submenuId)
                                                <div class="submenu-item mb-4">
                                                    <h6 class="text-muted">* {{ $submenuName }}</h6>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%">No</th>
                                                                    <th style="width: 30%">Nama Pengguna</th>
                                                                    <th style="width: 13%">Tampil Menu</th>
                                                                    <th style="width: 13%">Lihat</th>
                                                                    <th style="width: 13%">Tambah</th>
                                                                    <th style="width: 13%">Ubah</th>
                                                                    <th style="width: 13%">Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($levelData['users'] as $index => $user)
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ $user->nama_pengguna }}</td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_menu"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="menu_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_menu"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="menu">
                                                                                <label class="custom-control-label"
                                                                                    for="menu_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_view"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="view_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_view"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="view">
                                                                                <label class="custom-control-label"
                                                                                    for="view_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_create"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="create_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_create"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="create">
                                                                                <label class="custom-control-label"
                                                                                    for="create_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_update"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="update_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_update"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="update">
                                                                                <label class="custom-control-label"
                                                                                    for="update_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <input type="hidden"
                                                                                name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_delete"
                                                                                value="0">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input hak-akses-checkbox"
                                                                                    id="delete_{{ $user->user_id }}_{{ $submenuId }}"
                                                                                    name="set_hak_akses_{{ $user->user_id }}_{{ $submenuId }}_delete"
                                                                                    value="1" data-user="{{ $user->user_id }}"
                                                                                    data-menu="{{ $submenuId }}" data-hak="delete">
                                                                                <label class="custom-control-label"
                                                                                    for="delete_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            // Hilangkan tombol "Tambah Hak Akses" karena fitur ini tidak diperlukan lagi sesuai revisi

            $(document).on('click', '.set-hak-level', function () {
                let hakAksesKode = $(this).data('level');
                $('#hakAksesKode').val(hakAksesKode);
                $('#levelTitle').text($(this).data('name'));

                $.ajax({
                    url: `{{ url('/HakAkses/getHakAksesData') }}/${hakAksesKode}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let html = '';

                        Object.keys(data).forEach(menu_id => {
                            let akses = data[menu_id];

                            // Tambahkan baris untuk ha_menu di modal hak akses level
                            html += `
                            <tr>
                                <td>${akses.menu_utama}</td>
                                <td>${akses.sub_menu ?? 'Null'}</td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][menu]" ${akses.ha_menu ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][view]" ${akses.ha_view ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][create]" ${akses.ha_create ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][update]" ${akses.ha_update ? 'checked' : ''}></td>
                                <td class="text-center"><input type="checkbox" name="menu_akses[${menu_id}][delete]" ${akses.ha_delete ? 'checked' : ''}></td>
                            </tr>
                            `;
                        });

                        $('#menuList').html(html);
                        $('#modalHakAksesLevel').modal('show');
                    },
                    error: function () {
                        alert("Terjadi kesalahan, silakan coba lagi.");
                    }
                });
            });

            $('#btnSimpanHakAksesLevel').click(function () {
                $.ajax({
                    url: `{{ url('/HakAkses/updateData') }}`,
                    type: 'POST',
                    data: $('#formHakAksesLevel').serialize(),
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error("Terjadi kesalahan, silakan coba lagi.");
                    }
                });
            });

            // Muat hak akses saat halaman dimuat
            loadAllHakAkses();

            // Simpan semua perubahan
            $('#btn-save-all').click(function () {
                $('#form-hak-akses').submit();
            });

            // Fungsi untuk memuat hak akses
            function loadAllHakAkses() {
                $('.hak-akses-checkbox').each(function () {
                    const userId = $(this).data('user');
                    const menuId = $(this).data('menu');
                    const hak = $(this).data('hak');
                    const checkbox = $(this);

                    // Gunakan AJAX untuk mendapatkan data hak akses
                    $.ajax({
                        url: `{{ url('/HakAkses/getHakAksesData') }}/${userId}/${menuId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            // Periksa apakah data ditemukan dan nilai hak akses adalah 1
                            if (data && data['ha_' + hak] === 1) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        },
                        error: function (error) {
                            console.error('Error loading hak akses:', error);
                            checkbox.prop('checked', false); // Default tidak dicentang jika error
                        }
                    });
                });
            }

            // Toggle collapse untuk semua menu saat pertama kali
            $('.collapse').first().addClass('show');

            // Tambahkan toastr jika belum ada
            if (typeof toastr === 'undefined') {
                toastr = {
                    success: function (message) { alert('Sukses: ' + message); },
                    error: function (message) { alert('Error: ' + message); },
                    info: function (message) { alert('Info: ' + message); }
                };
            }
        });
    </script>
@endpush