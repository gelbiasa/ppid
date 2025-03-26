<div class="modal-header">
     <h5 class="modal-title">Tambah Detail Media Dinamis</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <form id="form-create-detail-media" enctype="multipart/form-data">
     <div class="modal-body">
         <div class="form-group">
             <label for="fk_m_media_dinamis">Kategori Media <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_m_media_dinamis" name="t_detail_media_dinamis[fk_m_media_dinamis]" required>
                 <option value="">-- Pilih Kategori Media --</option>
                 @foreach($kategoris as $kategori)
                     <option value="{{ $kategori->media_dinamis_id }}">{{ $kategori->md_kategori_media }}</option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="error-t_detail_media_dinamis.fk_m_media_dinamis"></div>
         </div>
         <div class="form-group">
          <label for="dm_judul_media">Judul Media <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="dm_judul_media" name="t_detail_media_dinamis[dm_judul_media]" 
                 placeholder="Masukkan judul media" required maxlength="255">
          <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_judul_media"></div>
      </div>
         
         <div class="form-group">
             <label for="dm_type_media">Tipe Media <span class="text-danger">*</span></label>
             <select class="form-control" id="dm_type_media" name="t_detail_media_dinamis[dm_type_media]" required onchange="toggleMediaInput()">
                 <option value="">-- Pilih Tipe Media --</option>
                 <option value="file">File</option>
                 <option value="link">Link</option>
             </select>
             <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_type_media"></div>
         </div>
         
       <div id="media-file-input" class="form-group" style="display: none;">
    <label for="media_file">Upload File <span class="text-danger">*</span></label>
    <div class="custom-file">
        <input type="file" class="custom-file-input" id="media_file" name="media_file">
        <label class="custom-file-label" for="media_file">Pilih file</label>
    </div>
    <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, GIF,SVG,WEBP, PDF.</small>
    <div class="invalid-feedback" id="error-media_file"></div>
    <div id="file-preview" class="mt-2 d-none">
        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
    </div>
</div>
         
         <div id="media-link-input" class="form-group" style="display: none;">
             <label for="dm_media_upload_link">Link Media <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="dm_media_upload_link" name="t_detail_media_dinamis[dm_media_upload]" placeholder="https://example.com/media">
             <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_media_upload"></div>
         </div>
         
         <div class="form-group">
             <label for="status_media">Status Media <span class="text-danger">*</span></label>
             <select class="form-control" id="status_media" name="t_detail_media_dinamis[status_media]" required>
                 <option value="aktif" selected>Aktif</option>
                 <option value="nonaktif">Nonaktif</option>
             </select>
             <div class="invalid-feedback" id="error-t_detail_media_dinamis.status_media"></div>
         </div>
     </div>
     
     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
         <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
     </div>
 </form>
 
 <script>
     function toggleMediaInput() {
         const mediaType = $('#dm_type_media').val();
         
         if (mediaType === 'file') {
             $('#media-file-input').show();
             $('#media-link-input').hide();
             $('#dm_media_upload_link').removeAttr('required');
         } else if (mediaType === 'link') {
             $('#media-file-input').hide();
             $('#media-link-input').show();
             $('#dm_media_upload_link').attr('required', true);
         } else {
             $('#media-file-input').hide();
             $('#media-link-input').hide();
         }
     }
 
     $(document).ready(function() {
         // Reset form on modal close
         $('#myModal').on('hidden.bs.modal', function() {
             $('#form-create-detail-media')[0].reset();
             $('.is-invalid').removeClass('is-invalid');
             $('#file-preview').addClass('d-none');
             $('.custom-file-label').text('Pilih file');
         });
         
         // Preview image before upload
         $('#media_file').on('change', function() {
            let file = this.files[0];
            $('.custom-file-label').text(file.name);
            
            if (file) {
                const fileExt = file.name.split('.').pop().toLowerCase();
                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg','webp'];
                const isImage = imageExtensions.includes(fileExt);
                
                // Reset preview
                $('#file-preview').addClass('d-none');
                $('#file-preview img').attr('src', '');
                
                if (isImage) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#file-preview').removeClass('d-none');
                        $('#file-preview img').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                } else if (fileExt === 'pdf') {
                    // For PDF, show a PDF icon or preview
                    $('#file-preview').removeClass('d-none');
                    $('#file-preview img').attr('src', '{{ asset("path/to/pdf-icon.png") }}');
                } else {
                    // For other file types, show a generic file icon
                    $('#file-preview').removeClass('d-none');
                    $('#file-preview img').attr('src', '{{ asset("path/to/file-icon.png") }}');
                }
            }
        });
         
         // Form submission
         $('#form-create-detail-media').on('submit', function(e) {
             e.preventDefault();
             
             // Reset error messages
             $('.is-invalid').removeClass('is-invalid');
             
             // Disable button to prevent multiple submissions
             $('#btn-save').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
             
             // Create FormData object to handle file uploads
             var formData = new FormData(this);
             
             
             // Submit form data via AJAX
             $.ajax({
                 url: '{{ url("adminweb/media-detail/createData") }}',
                 type: 'POST',
                 data: formData,
                 processData: false,  // Important for FormData
                 contentType: false,  // Important for FormData
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
                             var fieldName = key.replace(/\./g, '\\.');
                             $('#' + fieldName).addClass('is-invalid');
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