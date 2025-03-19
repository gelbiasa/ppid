<!-- views/AdminWeb/Footer/create.blade.php -->

<div class="modal-header">
    <h5 class="modal-title">Tambah Footer</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-footer" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="form-group">
            <label for="fk_m_kategori_footer">Kategori Footer <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_kategori_footer" name="fk_m_kategori_footer" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategoriFooters as $kategori)
                    <option value="{{ $kategori->kategori_footer_id }}">{{ $kategori->kt_footer_nama }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="error-fk_m_kategori_footer"></div>
        </div>
        
        <div class="form-group">
            <label for="f_judul_footer">Judul Footer <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="f_judul_footer" name="f_judul_footer" required maxlength="100">
            <div class="invalid-feedback" id="error-f_judul_footer"></div>
        </div>
        
        <div class="form-group">
            <label for="f_url_footer">URL Footer</label>
            <input type="text" class="form-control" id="f_url_footer" name="f_url_footer" maxlength="100" placeholder="https://example.com">
            <div class="invalid-feedback" id="error-f_url_footer"></div>
            <small class="form-text text-muted">Masukkan URL lengkap dengan http:// atau https://</small>
        </div>
        
        <div class="form-group">
            <label for="f_icon_footer">Icon Footer</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="f_icon_footer" name="f_icon_footer" accept="image/*">
                <label class="custom-file-label" for="f_icon_footer">Pilih file</label>
            </div>
            <div class="invalid-feedback" id="error-f_icon_footer"></div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, GIF, SVG. Ukuran maksimal: 2MB.</small>
            <div id="image-preview" class="mt-2 d-none">
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
            $('#form-create-footer')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('#image-preview').addClass('d-none');
            $('.custom-file-label').text('Pilih file');
        });
        
        // Preview image before upload
        $('#f_icon_footer').on('change', function() {
            let file = this.files[0];
            $('.custom-file-label').text(file.name);
            
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').removeClass('d-none');
                    $('#image-preview img').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form submission
        $('#form-create-footer').on('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            
            // Disable button to prevent multiple submissions
            $('#btn-save').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            
            // Create FormData object for file upload
            var formData = new FormData(this);
            
            // Submit form data via AJAX
            $.ajax({
                url: '{{ url("adminweb/footer/createData") }}',
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