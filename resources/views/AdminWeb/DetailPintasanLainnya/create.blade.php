@php
  use App\Models\Website\WebMenuModel;
  $detailPintasanLainnyaUrl = WebMenuModel::getDynamicMenuUrl('detail-pintasan-lainnya');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Tambah Detail Pintasan Lainnya Baru</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <form id="formCreateDetailPintasanLainnya" action="{{ url($detailPintasanLainnyaUrl . '/createData') }}" method="POST">
         @csrf
   
         <div class="form-group">
             <label for="fk_pintasan_lainnya">Kategori Pintasan <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_pintasan_lainnya" name="t_detail_pintasan_lainnya[fk_pintasan_lainnya]">
                 <option value="">-- Pilih Kategori Pintasan --</option>
                 @foreach ($pintasanLainnya as $pintasan)
                     <option value="{{ $pintasan->pintasan_lainnya_id }}">{{ $pintasan->tpl_nama_kategori }}</option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="fk_pintasan_lainnya_error"></div>
         </div>
   
         <div class="form-group">
             <label for="dpl_judul">Judul Pintasan <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="dpl_judul" name="t_detail_pintasan_lainnya[dpl_judul]" maxlength="100" placeholder="Masukkan judul pintasan">
             <div class="invalid-feedback" id="dpl_judul_error"></div>
         </div>
   
         <div class="form-group">
             <label for="dpl_url">URL Pintasan <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="dpl_url" name="t_detail_pintasan_lainnya[dpl_url]" maxlength="100" placeholder="Masukkan URL pintasan (contoh: https://example.com)">
             <div class="invalid-feedback" id="dpl_url_error"></div>
             <small class="form-text text-muted">Contoh: https://example.com</small>
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
         // Hapus error ketika input berubah
         $(document).on('input change', 'input, select, textarea', function() {
             $(this).removeClass('is-invalid');
             const errorId = `#${$(this).attr('id')}_error`;
             $(errorId).html('');
         });
   
         // Handle submit form with validation
         $('#btnSubmitForm').on('click', function() {
             // Reset semua error
             $('.is-invalid').removeClass('is-invalid');
             $('.invalid-feedback').html('');
             
             const form = $('#formCreateDetailPintasanLainnya');
             const formData = new FormData(form[0]);
             const button = $(this);
   
             // Ambil nilai input
             const kategoriPintasan = $('#fk_pintasan_lainnya').val().trim();
             const judulPintasan = $('#dpl_judul').val().trim();
             const urlPintasan = $('#dpl_url').val().trim();
   
             // Validasi client-side
             let isValid = true;
   
             // Validasi Kategori Pintasan
             if (kategoriPintasan === '') {
                 $('#fk_pintasan_lainnya').addClass('is-invalid');
                 $('#fk_pintasan_lainnya_error').html('Kategori Pintasan wajib dipilih.');
                 isValid = false;
             }
   
             // Validasi Judul Pintasan
             if (judulPintasan === '') {
                 $('#dpl_judul').addClass('is-invalid');
                 $('#dpl_judul_error').html('Judul Pintasan wajib diisi.');
                 isValid = false;
             } else if (judulPintasan.length > 100) {
                 $('#dpl_judul').addClass('is-invalid');
                 $('#dpl_judul_error').html('Maksimal 100 karakter.');
                 isValid = false;
             }
   
             // Validasi URL Pintasan
             if (urlPintasan === '') {
                 $('#dpl_url').addClass('is-invalid');
                 $('#dpl_url_error').html('URL Pintasan wajib diisi.');
                 isValid = false;
             } else if (urlPintasan.length > 100) {
                 $('#dpl_url').addClass('is-invalid');
                 $('#dpl_url_error').html('Maksimal 100 karakter.');
                 isValid = false;
             } else if (!isValidURL(urlPintasan)) {
                 $('#dpl_url').addClass('is-invalid');
                 $('#dpl_url_error').html('Format URL tidak valid.');
                 isValid = false;
             }
   
             // Jika validasi gagal, tampilkan pesan error dan batalkan pengiriman form
             if (!isValid) {
                 Swal.fire({
                     icon: 'error',
                     title: 'Validasi Gagal',
                     text: 'Mohon periksa kembali input Anda.'
                 });
                 return;
             }
   
             // Tampilkan loading state pada tombol submit
             button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
   
             // Kirim data form menggunakan AJAX
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
                             // Tampilkan pesan error pada masing-masing field
                             $.each(response.errors, function(key, value) {
                                 // Untuk t_detail_pintasan_lainnya fields
                                 if (key.startsWith('t_detail_pintasan_lainnya.')) {
                                     const fieldName = key.replace('t_detail_pintasan_lainnya.', '');
                                     $(`#${fieldName}`).addClass('is-invalid');
                                     $(`#${fieldName}_error`).html(value[0]);
                                 } else {
                                     // Untuk field biasa
                                     $(`#${key}`).addClass('is-invalid');
                                     $(`#${key}_error`).html(value[0]);
                                 }
                             });
   
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Validasi Gagal',
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
                     console.error('Error:', xhr);
                     Swal.fire({
                         icon: 'error',
                         title: 'Gagal',
                         text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                     });
                 },
                 complete: function() {
                     // Kembalikan tombol submit ke keadaan semula
                     button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                 }
             });
         });
   
         // Function to validate URL
         function isValidURL(url) {
             try {
                 new URL(url);
                 return true;
             } catch (_) {
                 return false;
             }
         }
     });
   </script>