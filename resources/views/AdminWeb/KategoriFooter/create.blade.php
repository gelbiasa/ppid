<!-- views/AdminWeb/KategoriFooter/create.blade.php -->

<div class="modal-header">
    <h5 class="modal-title">Tambah Kategori Footer</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-kategori-footer">
    <div class="modal-body">
        <div class="form-group">
            <label for="kt_footer_kode">Kode Footer <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kt_footer_kode" name="kt_footer_kode" required maxlength="20">
            <div class="invalid-feedback" id="error-kt_footer_kode"></div>
        </div>
        
        <div class="form-group">
            <label for="kt_footer_nama">Nama Footer <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kt_footer_nama" name="kt_footer_nama" required maxlength="100">
            <div class="invalid-feedback" id="error-kt_footer_nama"></div>
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
            $('#form-create-kategori-footer')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
        });
        
        // Form submission
        $('#form-create-kategori-footer').on('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            
            // Disable button to prevent multiple submissions
            $('#btn-save').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            
            // Submit form data via AJAX
            $.ajax({
                url: '{{ url("adminweb/kategori-footer/createData") }}',
                type: 'POST',
                data: $(this).serialize(),
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
                            $('#table_kategori_footer').DataTable().ajax.reload();
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