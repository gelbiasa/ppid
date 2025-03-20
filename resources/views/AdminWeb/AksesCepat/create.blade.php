<!-- views/AdminWeb/AksesCepat/create.blade.php -->

<div class="modal-header">
    <h5 class="modal-title">Tambah Akses Cepat</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-akses-cepat" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="form-group">
            <label for="fk_m_kategori_akses">Kategori Akses Cepat</label>
            <input type="hidden" name="fk_m_kategori_akses" value="{{ $kategoriAkses->kategori_akses_id }}">
            <input type="text" class="form-control" value="{{ $kategoriAkses->mka_judul_kategori }}" readonly>
            <div class="invalid-feedback" id="error-fk_m_kategori_akses"></div>
        </div>

        <div class="form-group">
            <label for="ac_judul">Judul Akses Cepat <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ac_judul" name="ac_judul" required maxlength="100">
            <div class="invalid-feedback" id="error-ac_judul"></div>
        </div>

        <div class="form-group">
            <label for="ac_url">URL Akses Cepat</label>
            <input type="text" class="form-control" id="ac_url" name="ac_url" maxlength="100"
                placeholder="https://contoh.com">
            <div class="invalid-feedback" id="error-ac_url"></div>
            <small class="form-text text-muted">Masukkan URL lengkap dengan http:// atau https://</small>
        </div>

        <div class="form-group">
            <label for="ac_static_icon">Icon Statis Akses Cepat</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="ac_static_icon" name="ac_static_icon"
                    accept="image/*">
                <label class="custom-file-label" for="ac_static_icon">Pilih file</label>
            </div>
            <div class="invalid-feedback" id="error-ac_static_icon"></div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG. Ukuran maksimal: 2.5MB.</small>
            <div id="static-image-preview" class="mt-2 d-none">
                <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
            </div>
        </div>

        <div class="form-group">
            <label for="ac_animation_icon">Icon Animasi Akses Cepat (GIF)</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="ac_animation_icon" name="ac_animation_icon"
                    accept="image/gif">
                <label class="custom-file-label" for="ac_animation_icon">Pilih file</label>
            </div>
            <div class="invalid-feedback" id="error-ac_animation_icon"></div>
            <small class="form-text text-muted">Hanya file GIF. Ukuran maksimal: 2.5MB.</small>
            <div id="animation-image-preview" class="mt-2 d-none">
                <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Reset form on modal close
        $('#myModal').on('hidden.bs.modal', function() {
            $('#form-create-akses-cepat')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('#static-image-preview, #animation-image-preview').addClass('d-none');
            $('.custom-file-label').text('Pilih file');
        });

        // Preview static icon before upload
        $('#ac_static_icon').on('change', function() {
            let file = this.files[0];
            $('.custom-file-label[for="ac_static_icon"]').text(file.name);

            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#static-image-preview').removeClass('d-none');
                    $('#static-image-preview img').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#ac_animation_icon').on('change', function() {
            let file = this.files[0];
            if (file) {
                let fileSizeMB = file.size / (1024 * 1024);
                $('.custom-file-label[for="ac_animation_icon"]').text(
                    file.name + ' (' + fileSizeMB.toFixed(2) + ' MB)'
                );

                // Tampilkan peringatan jika file melebihi 3MB
                if (fileSizeMB > 3) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Ukuran file ' + fileSizeMB.toFixed(2) + ' MB melebihi batas 3MB',
                        icon: 'warning'
                    });
                    // Reset input file
                    $(this).val('');
                    $('.custom-file-label[for="ac_animation_icon"]').text('Pilih file');
                    $('#animation-image-preview').addClass('d-none');
                } else {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#animation-image-preview').removeClass('d-none');
                        $('#animation-image-preview img').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            }
        });

        // Form submission
        $('#form-create-akses-cepat').on('submit', function(e) {
            e.preventDefault();

            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');

            // Disable button to prevent multiple submissions
            $('#btn-save').attr('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            // Create FormData object for file upload
            var formData = new FormData(this);

            // Submit form data via AJAX
            $.ajax({
                url: '{{ url('adminweb/akses-cepat/createData') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            // Close modal and refresh data table
                            $('#myModal').modal('hide');
                            reloadTable();
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Enable button
                        $('#btn-save').attr('disabled', false).html('Simpan');
                    }
                },
                error: function(xhr) {
                    // Enable button
                    $('#btn-save').attr('disabled', false).html('Simpan');

                    // Handle validation errors
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#error-' + key).text(value[0]);
                        });
                    } else {
                        // Show general error message
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
