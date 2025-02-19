<form action="{{ url('/adminweb/menu-utama/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Menu Utama</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Nama Input -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="wm_menu_nama" id="name" class="form-control" required>
                    <small id="error-name" class="error-text form-text text-danger"></small>
                </div>

                <!-- Status Menu (Aktif / Nonaktif) -->
                <div class="form-group">
                    <label>Status Menu</label>
                    <select name="wm_status_menu" class="form-control" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    <small id="error-wm_status_menu" class="error-text form-text text-danger"></small>
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
        // Slugify otomatis
        $('#name').on('input', function() {
            var menuNama = $(this).val();
            var menuUrl = menuNama.trim().toLowerCase()
                .replace(/[^a-z0-9]+/g, '-') // Ganti karakter non-alfanumerik dengan "-"
                .replace(/^-+|-+$/g, ''); // Hapus "-" di awal/akhir
            $('#wm_menu_url').val(menuUrl);
        });

        // Validasi Form
        $("#form-tambah").validate({
            rules: {
                wm_menu_nama: { required: true, minlength: 3, maxlength: 60 }
            },
            messages: {
                wm_menu_nama: {
                    required: "Nama harus diisi.",
                    minlength: "Nama minimal 3 karakter.",
                    maxlength: "Nama maksimal 60 karakter."
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            $('#myModal').modal('hide'); // Tutup modal
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            $('#form-tambah')[0].reset(); // Reset form
                            dataMenu.ajax.reload(); // Reload DataTables
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
                    },
                    error: function(xhr, success, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menyimpan data: ' + error
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>

