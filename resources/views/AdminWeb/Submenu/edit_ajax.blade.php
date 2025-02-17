@empty($submenu)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/adminweb/submenu') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/adminweb/submenu/' . $submenu->web_menu_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sub Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Menu Utama Selection -->
                <div class="form-group">
                    <label>Menu Utama</label>
                    <select name="wm_parent_id" class="form-control" required>
                        <option value="">Pilih Menu Utama</option>
                        @foreach($subMenus as $menu)
                            <option value="{{ $menu->web_menu_id }}" {{ $submenu->wm_parent_id == $menu->web_menu_id ? 'selected' : '' }}>
                                {{ $menu->wm_menu_nama }}
                            </option>
                        @endforeach
                    </select>
                    <small id="error-wm_parent_id" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama Sub Menu</label>
                    <input type="text" name="wm_menu_nama" id="name" class="form-control" value="{{ $submenu->wm_menu_nama }}" required>
                    <small id="error-name" class="error-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Status Menu</label>
                    <select name="wm_status_menu" class="form-control" required>
                        <option value="aktif" {{ $submenu->wm_status_menu == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $submenu->wm_status_menu == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <small id="error-wm_status_menu" class="error-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-edit").validate({
            rules: {
                wm_parent_id: { required: true },
                wm_menu_nama: { required: true, minlength: 3, maxlength: 60 }
            },
            messages: {
                wm_parent_id: {
                    required: "Menu utama harus dipilih."
                },
                wm_menu_nama: {
                    required: "Nama sub menu harus diisi.",
                    minlength: "Nama minimal 3 karakter.",
                    maxlength: "Nama maksimal 60 karakter."
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataSubmenu.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endempty