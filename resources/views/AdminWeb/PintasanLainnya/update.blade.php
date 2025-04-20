<div class="modal-header">
     <h5 class="modal-title">Edit Pintasan Lainnya</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <form id="formUpdatePintasanLainnya" action="{{ url('adminweb/pintasan-lainnya/updateData/' . $pintasanLainnya->pintasan_lainnya_id) }}" method="POST">
         @csrf
         
         <div class="form-group">
             <label for="fk_m_kategori_akses">Kategori Akses</label>
             <input type="hidden" id="fk_m_kategori_akses" name="t_pintasan_lainnya[fk_m_kategori_akses]" 
                    value="{{ $pintasanLainnya->fk_m_kategori_akses }}">
             <input type="text" class="form-control" value="{{ $pintasanLainnya->kategoriAkses->mka_judul_kategori }}" readonly>
             <div class="invalid-feedback" id="error-fk_m_kategori_akses"></div>
         </div>
   
         <div class="form-group">
             <label for="tpl_nama_kategori">Nama Pintasan <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="tpl_nama_kategori" 
                    name="t_pintasan_lainnya[tpl_nama_kategori]" 
                    maxlength="255" 
                    value="{{ $pintasanLainnya->tpl_nama_kategori }}"
                    required>
             <div class="invalid-feedback" id="tpl_nama_kategori_error"></div>
         </div>
     </form>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-success" id="btnUpdateForm">
         <i class="fas fa-save mr-1"></i> Perbarui
     </button>
   </div>
   
   <script>
   $(document).ready(function () {
     // Clear validation errors on input
     $(document).on('input change', 'input, select, textarea', function() {
         $(this).removeClass('is-invalid');
         const errorId = `#${$(this).attr('id')}_error`;
         $(errorId).html('');
     });
   
     // Form submission handler
     $('#btnUpdateForm').on('click', function() {
         // Reset previous validation errors
         $('.is-invalid').removeClass('is-invalid');
         $('.invalid-feedback').html('');
         
         // Validate required fields
         const form = $('#formUpdatePintasanLainnya');
         const inputs = form.find('input[required]');
         let isValid = true;
   
         inputs.each(function() {
             if (!$(this).val().trim()) {
                 $(this).addClass('is-invalid');
                 $(`#${$(this).attr('id')}_error`).html('Field ini wajib diisi');
                 isValid = false;
             }
         });
   
         // Stop if validation fails
         if (!isValid) {
             return;
         }
   
         // Prepare form data
         const formData = new FormData(form[0]);
         const button = $(this);
         
         // Disable button and show loading
         button.html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...').attr('disabled', true);
         
         // AJAX submission
         $.ajax({
             url: form.attr('action'),
             type: 'POST',
             data: formData,
             processData: false,
             contentType: false,
             success: function(response) {
                 if (response.success) {
                     // Close modal and reload table on success
                     $('#myModal').modal('hide');
                     
                     // Assuming reloadTable() is defined elsewhere
                     if (typeof reloadTable === 'function') {
                         reloadTable();
                     }
                     
                     // Success notification
                     Swal.fire({
                         icon: 'success',
                         title: 'Berhasil',
                         text: response.message || 'Data berhasil diperbarui'
                     });
                 } else {
                     // Handle validation errors
                     if (response.errors) {
                         $.each(response.errors, function(key, value) {
                             let fieldName = key.replace('t_pintasan_lainnya.', '');
                             $(`#${fieldName}`).addClass('is-invalid');
                             $(`#${fieldName}_error`).html(value[0]);
                         });
                         
                         // Error notification
                         Swal.fire({
                             icon: 'error',
                             title: 'Validasi Gagal',
                             text: 'Mohon periksa kembali input Anda'
                         });
                     } else {
                         // General error notification
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal',
                             text: response.message || 'Terjadi kesalahan saat memperbarui data'
                         });
                     }
                 }
             },
             error: function(xhr) {
                 // Detailed error logging
                 console.error('Submission Error:', xhr);
                 
                 // Error notification
                 Swal.fire({
                     icon: 'error',
                     title: 'Gagal',
                     text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.'
                 });
             },
             complete: function() {
                 // Restore button state
                 button.html('<i class="fas fa-save mr-1"></i> Perbarui').attr('disabled', false);
             }
         });
     });
   });
   </script>