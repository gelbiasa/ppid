<!-- views/AdminWeb/AksesCepat/update.blade.php -->

<div class="modal-header">
     <h5 class="modal-title">Edit Akses Cepat</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <form id="form-update-akses-cepat" enctype="multipart/form-data">
     <div class="modal-body">
          <div class="form-group">
               <label for="fk_m_kategori_akses">Kategori Akses Cepat</label>
               <input type="hidden" name="fk_m_kategori_akses" value="{{ $kategoriAkses->kategori_akses_id }}">
               <input type="text" class="form-control" value="{{ $kategoriAkses->mka_judul_kategori }}" readonly>
               <div class="invalid-feedback" id="error-fk_m_kategori_akses"></div>
           </div>
         
         <div class="form-group">
             <label for="ac_judul">Judul Akses Cepat <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="ac_judul" name="ac_judul" required maxlength="100" value="{{ $aksesCepat->ac_judul }}">
             <div class="invalid-feedback" id="error-ac_judul"></div>
         </div>
         
         <div class="form-group">
             <label for="ac_url">URL Akses Cepat <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="ac_url" name="ac_url" required maxlength="100" placeholder="https://contoh.com" value="{{ $aksesCepat->ac_url }}">
             <div class="invalid-feedback" id="error-ac_url"></div>
             <small class="form-text text-muted">Masukkan URL lengkap dengan http:// atau https://</small>
         </div>
         
         <div class="form-group">
             <label for="ac_static_icon">Icon Statis Akses Cepat</label>
             <div class="custom-file">
                 <input type="file" class="custom-file-input" id="ac_static_icon" name="ac_static_icon" accept="image/*">
                 <label class="custom-file-label" for="ac_static_icon">{{ $aksesCepat->ac_static_icon ? $aksesCepat->ac_static_icon : 'Pilih file' }}</label>
             </div>
             <div class="invalid-feedback" id="error-ac_static_icon"></div>
             <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG. Ukuran maksimal: 2.5MB.</small>
             
             @if($aksesCepat->ac_static_icon)
             <div id="current-static-image" class="mt-2">
                 <p>Icon statis saat ini:</p>
                 <img src="{{ asset('storage/' . $aksesCepat::STATIC_ICON_PATH . '/' . $aksesCepat->ac_static_icon) }}" alt="Current Static Icon" class="img-thumbnail" style="height: 100px;">
             </div>
             @endif
             
             <div id="static-image-preview" class="mt-2 d-none">
                 <p>Icon statis baru:</p>
                 <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
             </div>
         </div>
         
         <div class="form-group">
          <label for="ac_animation_icon">Icon Hover Akses Cepat</label>
          <div class="custom-file">
              <input type="file" class="custom-file-input" id="ac_animation_icon" name="ac_animation_icon" accept="image/*">
              <label class="custom-file-label" for="ac_animation_icon">{{ $aksesCepat->ac_animation_icon ? $aksesCepat->ac_animation_icon : 'Pilih file' }}</label>
          </div>
          <div class="invalid-feedback" id="error-ac_animation_icon"></div>
          <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG, GIF. Ukuran maksimal: 2.5MB.</small>
          
          @if($aksesCepat->ac_animation_icon)
          <div id="current-animation-image" class="mt-2">
              <p>Icon animasi saat ini:</p>
              <img src="{{ asset('storage/' . $aksesCepat::ANIMATION_ICON_PATH . '/' . $aksesCepat->ac_animation_icon) }}" alt="Current Animation Icon" class="img-thumbnail" style="height: 100px;">
          </div>
          @endif
          
          <div id="animation-image-preview" class="mt-2 d-none">
              <p>Icon animasi baru:</p>
              <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
          </div>
      </div>
     </div>
     
     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
         <button type="submit" class="btn btn-primary" id="btn-update">Perbarui</button>
     </div>
 </form>
 
 <script>
     $(document).ready(function() {
         // Preview static icon before upload
         $('#ac_static_icon').on('change', function() {
             let file = this.files[0];
             if (file) {
                 let fileSizeMB = file.size / (1024 * 1024);
                 $('.custom-file-label[for="ac_static_icon"]').text(
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
                     $('.custom-file-label[for="ac_static_icon"]').text('Pilih file');
                     $('#static-image-preview').addClass('d-none');
                 } else {
                     let reader = new FileReader();
                     reader.onload = function(e) {
                         $('#static-image-preview').removeClass('d-none');
                         $('#static-image-preview img').attr('src', e.target.result);
                     }
                     reader.readAsDataURL(file);
                 }
             }
         });
         
         // Preview animation icon before upload
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
         $('#form-update-akses-cepat').on('submit', function(e) {
             e.preventDefault();
             
             // Reset error messages
             $('.is-invalid').removeClass('is-invalid');
             
             // Disable button to prevent multiple submissions
             $('#btn-update').attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...');
             
             // Create FormData object for file upload
             var formData = new FormData(this);
             
             // Submit form data via AJAX
             $.ajax({
                 url: '{{ url("adminweb/akses-cepat/updateData/{$aksesCepat->akses_cepat_id}") }}',
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
                         $('#btn-update').attr('disabled', false).html('Perbarui');
                     }
                 },
                 error: function(xhr) {
                     // Enable button
                     $('#btn-update').attr('disabled', false).html('Perbarui');
                     
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
                             text: 'Terjadi kesalahan saat memperbarui data.',
                             icon: 'error',
                             confirmButtonText: 'OK'
                         });
                     }
                 }
             });
         });
     });
 </script>