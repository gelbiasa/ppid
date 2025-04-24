<div class="modal-header">
     <h5 class="modal-title">Tambah IP Dinamis Tabel Baru</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <form id="formCreateIpDinamisTabel" action="{{ url('adminweb/informasipublik/IpDinamisTabel/createData') }}" method="POST">
       @csrf
   
       <div class="form-group">
         <label for="ip_nama_submenu">Nama Submenu <span class="text-danger">*</span></label>
         <input type="text" class="form-control" id="ip_nama_submenu" name="m_ip_dinamis_tabel[ip_nama_submenu]" maxlength="100">
         <div class="invalid-feedback" id="ip_nama_submenu_error"></div>
       </div>
   
       <div class="form-group">
         <label for="ip_judul">Judul <span class="text-danger">*</span></label>
         <input type="text" class="form-control" id="ip_judul" name="m_ip_dinamis_tabel[ip_judul]" maxlength="100">
         <div class="invalid-feedback" id="ip_judul_error"></div>
       </div>
     </form>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-success" id="btnSubmitForm">
       <i class="fas fa-save mr-1"></i> Simpan
     </button>
   </div>
   
   <script>
     $(document).ready(function () {
       // Menghapus error saat user mengedit input
       $(document).on('input change', 'input, select, textarea', function() {
         $(this).removeClass('is-invalid');
         const errorId = `#${$(this).attr('id')}_error`;
         $(errorId).html('');
       });
   
       function validateForm() {
         let isValid = true;
   
         const namaSubmenu = $('#ip_nama_submenu').val().trim();
         const judul = $('#ip_judul').val().trim();
   
         if (namaSubmenu === '') {
           $('#ip_nama_submenu').addClass('is-invalid');
           $('#ip_nama_submenu_error').html('Nama Submenu wajib diisi.');
           isValid = false;
         } else if (namaSubmenu.length > 100) {
           $('#ip_nama_submenu').addClass('is-invalid');
           $('#ip_nama_submenu_error').html('Maksimal 100 karakter.');
           isValid = false;
         }
   
         if (judul === '') {
           $('#ip_judul').addClass('is-invalid');
           $('#ip_judul_error').html('Judul wajib diisi.');
           isValid = false;
         } else if (judul.length > 100) {
           $('#ip_judul').addClass('is-invalid');
           $('#ip_judul_error').html('Maksimal 100 karakter.');
           isValid = false;
         }
   
         return isValid;
       }
   
       $('#btnSubmitForm').on('click', function() {
         // Bersihkan error
         $('.is-invalid').removeClass('is-invalid');
         $('.invalid-feedback').html('');
   
         // Validasi form terlebih dahulu
         if (!validateForm()) {
           Swal.fire({
             icon: 'error',
             title: 'Terjadi Kesalahan',
             text: 'Mohon periksa kembali input Anda.'
           });
           return;
         }
   
         const form = $('#formCreateIpDinamisTabel');
         const formData = new FormData(form[0]);
         const button = $(this);
         
         button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
         
         $.ajax({
           url: form.attr('action'),
           type: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
             if (response.success) {
               $('#myModal').modal('hide');
               reloadTable();
               Swal.fire({
                 icon: 'success',
                 title: 'Berhasil',
                 text: response.message
               });
             } else {
               if (response.errors) {
                 $.each(response.errors, function(key, value) {
                   $(`#${key}`).addClass('is-invalid');
                   $(`#${key}_error`).html(value[0]);
                 });
                 Swal.fire({
                   icon: 'error',
                   title: 'Terjadi Kesalahan',
                   text: 'Mohon periksa kembali input Anda'
                 });
               } else {
                 Swal.fire({
                   icon: 'error',
                   title: 'Gagal',
                   text: response.message || 'Terjadi kesalahan saat menyimpan data'
                 });
               }
             }
           },
           error: function(xhr) {
             Swal.fire({
               icon: 'error',
               title: 'Gagal',
               text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
             });
           },
           complete: function() {
             button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
           }
         });
       });
     });
   </script>