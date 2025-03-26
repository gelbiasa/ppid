<div class="modal-header">
     <h5 class="modal-title">Tambah Media Dinamis</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <form id="form-create-media-dinamis">
     <div class="modal-body">
         <div class="form-group">
             <label for="md_kategori_media">Kategori Media <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="md_kategori_media" name="m_media_dinamis[md_kategori_media]" required maxlength="100">
             <div class="invalid-feedback" id="error-md_kategori_media"></div>
         </div>
     </div>
     
     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
         <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
     </div>
 </form>
 
 <script>
     $(document).ready(function() {
         // Form submission
         $('#form-create-media-dinamis').on('submit', function(e) {
             e.preventDefault();
             
             // Reset error messages
             $('.is-invalid').removeClass('is-invalid');
             
             // Disable button to prevent multiple submissions
             $('#btn-save').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
             
             // Submit form data via AJAX
             $.ajax({
                 url: '{{ url("adminweb/media-dinamis/createData") }}',
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