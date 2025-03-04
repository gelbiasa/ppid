@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pengaturan Hak Akses</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" id="btn-save-all">
                <i class="fas fa-save"></i> Simpan Semua Perubahan
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

        <form action="{{ url('/simpanHakAkses') }}" method="POST" id="form-hak-akses">
            @csrf
            <div class="accordion" id="accordionHakAkses">
                @foreach($levelUsers as $levelKode => $levelData)
                <div class="card">
                    <div class="card-header" id="heading{{ $levelKode }}">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                data-target="#collapse{{ $levelKode }}" aria-expanded="true"
                                aria-controls="collapse{{ $levelKode }}">
                                <strong>{{ $levelData['nama'] }} ({{ $levelKode }})</strong>
                            </button>
                        </h2>
                    </div>

                    <div id="collapse{{ $levelKode }}" class="collapse" aria-labelledby="heading{{ $levelKode }}"
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
                                                    <th style="width: 35%">Nama Pengguna</th>
                                                    <th style="width: 15%">Lihat</th>
                                                    <th style="width: 15%">Tambah</th>
                                                    <th style="width: 15%">Ubah</th>
                                                    <th style="width: 15%">Hapus</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($levelData['users'] as $index => $user)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $user->nama_pengguna }}</td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input hak-akses-checkbox"
                                                                id="view_{{ $user->user_id }}_{{ $submenuId }}"
                                                                name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_view"
                                                                data-user="{{ $user->user_id }}"
                                                                data-menu="{{ $submenuId }}" data-hak="view">
                                                            <label class="custom-control-label"
                                                                for="view_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input hak-akses-checkbox"
                                                                id="create_{{ $user->user_id }}_{{ $submenuId }}"
                                                                name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_create"
                                                                data-user="{{ $user->user_id }}"
                                                                data-menu="{{ $submenuId }}" data-hak="create">
                                                            <label class="custom-control-label"
                                                                for="create_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input hak-akses-checkbox"
                                                                id="update_{{ $user->user_id }}_{{ $submenuId }}"
                                                                name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_update"
                                                                data-user="{{ $user->user_id }}"
                                                                data-menu="{{ $submenuId }}" data-hak="update">
                                                            <label class="custom-control-label"
                                                                for="update_{{ $user->user_id }}_{{ $submenuId }}"></label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input hak-akses-checkbox"
                                                                id="delete_{{ $user->user_id }}_{{ $submenuId }}"
                                                                name="hak_akses_{{ $user->user_id }}_{{ $submenuId }}_delete"
                                                                data-user="{{ $user->user_id }}"
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
    $(document).ready(function() {
        // Muat hak akses saat halaman dimuat
        loadAllHakAkses();

        // Simpan semua perubahan
        $('#btn-save-all').click(function() {
            $('#form-hak-akses').submit();
        });

        // Fungsi untuk memuat hak akses
        function loadAllHakAkses() {
            $('.hak-akses-checkbox').each(function() {
                const userId = $(this).data('user');
                const menuId = $(this).data('menu');
                const hak = $(this).data('hak');
                const checkbox = $(this);

                // Gunakan AJAX untuk mendapatkan data hak akses
                $.ajax({
                    url: `{{ url('/getHakAkses') }}/${userId}/${menuId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Periksa apakah data ditemukan dan nilai hak akses adalah 1
                        if (data && data['ha_' + hak] === 1) {
                            checkbox.prop('checked', true);
                        } else {
                            checkbox.prop('checked', false);
                        }
                    },
                    error: function(error) {
                        console.error('Error loading hak akses:', error);
                        checkbox.prop('checked', false); // Default tidak dicentang jika error
                    }
                });
            });
        }

        // Toggle collapse untuk semua menu saat pertama kali
        $('.collapse').first().addClass('show');
    });
</script>
@endpush