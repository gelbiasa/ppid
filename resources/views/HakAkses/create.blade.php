@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <form id="formTambahHakAkses">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Level Pengguna <span class="text-danger">*</span></label>
                        <select name="hak_akses_kode" id="levelSelect" class="form-control" required>
                            <option value="">Pilih Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->hak_akses_kode }}">{{ $level->hak_akses_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Menu <span class="text-danger">*</span></label>
                        <select name="menu_id" id="menuSelect" class="form-control" required disabled>
                            <option value="">Pilih Menu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tampilkan Menu <span class="text-danger">*</span></label>
                        <select name="show_menu" id="showMenuSelect" class="form-control" required>
                            <option value="">Pilih Status Menu</option>
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Buat Hak Akses
            </button>
            <a href="{{ url('/HakAkses') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Ketika level dipilih, ambil menu sesuai level
    $('#levelSelect').on('change', function() {
        const hakAksesKode = $(this).val();
        const menuSelect = $('#menuSelect');
        const showMenuSelect = $('#showMenuSelect');

        // Reset menu select
        menuSelect.empty().append('<option value="">Pilih Menu</option>');
        menuSelect.prop('disabled', true);
        showMenuSelect.val('');

        if (hakAksesKode) {
            $.ajax({
                url: "{{ url('/get-menus') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    hak_akses_kode: hakAksesKode
                },
                success: function(response) {
                    // Tambahkan menu ke dropdown
                    response.forEach(function(menu) {
                        // Menu utama
                        menuSelect.append(`<option value="${menu.web_menu_id}">${menu.wm_menu_nama}</option>`);
                        
                        // Submenu
                        if (menu.children && menu.children.length > 0) {
                            menu.children.forEach(function(submenu) {
                                menuSelect.append(`<option value="${submenu.web_menu_id}">- ${submenu.wm_menu_nama}</option>`);
                            });
                        }
                    });

                    menuSelect.prop('disabled', false);
                },
                error: function() {
                    toastr.error('Gagal mengambil menu');
                }
            });
        }
    });

    // Submit form tambah hak akses
    $('#formTambahHakAkses').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ url('/store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Redirect atau reset form
                    setTimeout(() => {
                        window.location.href = "{{ url('/HakAkses') }}";
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Gagal menambahkan hak akses');
            }
        });
    });
});
</script>
@endpush