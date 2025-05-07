@php
  use App\Models\Website\WebMenuModel;
  $detailRegulasiUrl = WebMenuModel::getDynamicMenuUrl('detail-regulasi');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Edit Regulasi</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <form id="formUpdateRegulasi" action="{{ url($detailRegulasiUrl . '/updateData/' . $regulasi->regulasi_id) }}" method="POST"
         enctype="multipart/form-data">
         @csrf
 
         <div class="form-group">
             <label for="fk_t_kategori_regulasi">Kategori Regulasi <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_t_kategori_regulasi" name="t_regulasi[fk_t_kategori_regulasi]">
                 <option value="">-- Pilih Kategori --</option>
                 @foreach($kategoriRegulasi as $kategori)
                     <option value="{{ $kategori->kategori_reg_id }}" {{ $regulasi->fk_t_kategori_regulasi == $kategori->kategori_reg_id ? 'selected' : '' }}>
                         {{ $kategori->kr_nama_kategori }} ({{ $kategori->kr_kategori_reg_kode }})
                     </option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="fk_t_kategori_regulasi_error"></div>
         </div>
 
         <div class="form-group">
             <label for="reg_judul">Judul Regulasi <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="reg_judul" name="t_regulasi[reg_judul]" maxlength="255" value="{{ $regulasi->reg_judul }}">
             <div class="invalid-feedback" id="reg_judul_error"></div>
         </div>
 
         <div class="form-group">
             <label for="reg_sinopsis">Sinopsis <span class="text-danger">*</span></label>
             <textarea class="form-control" id="reg_sinopsis" name="t_regulasi[reg_sinopsis]" rows="4">{{ $regulasi->reg_sinopsis }}</textarea>
             <div class="invalid-feedback" id="reg_sinopsis_error"></div>
         </div>
 
         <div class="form-group">
             <label for="reg_tipe_dokumen">Tipe Dokumen <span class="text-danger">*</span></label>
             <select class="form-control" id="reg_tipe_dokumen" name="t_regulasi[reg_tipe_dokumen]">
                 <option value="file" {{ $regulasi->reg_tipe_dokumen == 'file' ? 'selected' : '' }}>File</option>
                 <option value="link" {{ $regulasi->reg_tipe_dokumen == 'link' ? 'selected' : '' }}>Link</option>
             </select>
             <div class="invalid-feedback" id="reg_tipe_dokumen_error"></div>
         </div>
 
         <div class="form-group" id="fileUploadDiv" {{ $regulasi->reg_tipe_dokumen == 'link' ? 'style=display:none;' : '' }}>
            <label for="reg_dokumen_file">File Dokumen</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="reg_dokumen_file" name="reg_dokumen_file">
                <label class="custom-file-label" for="reg_dokumen_file">Pilih file baru</label>
            </div>
            <small class="form-text text-muted">Format yang diizinkan: PDF, DOC, DOCX. Ukuran maksimal: 5MB</small>
            <div class="invalid-feedback" id="reg_dokumen_file_error"></div>
            
            @if($regulasi->reg_tipe_dokumen == 'file')
            <p class="mb-1">File saat ini:</p>
                <a href="{{ Storage::url($regulasi->reg_dokumen) }}" target="_blank" class="btn btn-sm btn-primary">
                  <i class="fas fa-file-pdf"></i> Lihat Dokumen
                </a>
            </div>
            @endif
         </div>
 
         <div class="form-group" id="linkUrlDiv" {{ $regulasi->reg_tipe_dokumen == 'file' ? 'style=display:none;' : '' }}>
             <label for="reg_dokumen">URL Dokumen <span class="text-danger">*</span></label>
             <input type="url" class="form-control" id="reg_dokumen" name="t_regulasi[reg_dokumen]" 
                 placeholder="https://..." value="{{ $regulasi->reg_tipe_dokumen == 'link' ? $regulasi->reg_dokumen : '' }}">
             <div class="invalid-feedback" id="reg_dokumen_error"></div>
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
     $(document).ready(function() {
         // Handle dokumen tipe toggle menggunakan dropdown
         $('#reg_tipe_dokumen').on('change', function() {
             if ($(this).val() === 'file') {
                 $('#fileUploadDiv').show();
                 $('#linkUrlDiv').hide();
                 // Reset nilai link saat beralih ke file
                 $('#reg_dokumen').val('');
             } else {
                 $('#fileUploadDiv').hide();
                 $('#linkUrlDiv').show();
                 // Reset file upload saat beralih ke link
                 $('#reg_dokumen_file').val('');
                 $('.custom-file-label').text('Pilih file baru');
             }
         });
 
         // Tampilkan nama file yang dipilih
         $('#reg_dokumen_file').on('change', function() {
             var fileName = $(this).val().split('\\').pop();
             $(this).next('.custom-file-label').html(fileName || 'Pilih file baru');
         });
 
         // Hapus error ketika input berubah
         $(document).on('input change', 'input, select, textarea', function() {
             $(this).removeClass('is-invalid');
             const errorId = `#${$(this).attr('id')}_error`;
             $(errorId).html('');
         });
 
         // Handle submit form
         $('#btnSubmitForm').on('click', function() {
             // Reset semua error
             $('.is-invalid').removeClass('is-invalid');
             $('.invalid-feedback').html('');
 
             const form = $('#formUpdateRegulasi');
             const formData = new FormData(form[0]);
             const button = $(this);
 
             // Tampilkan loading state pada tombol submit
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
                             // Tampilkan pesan error pada masing-masing field
                             $.each(response.errors, function(key, value) {
                                 // Untuk t_regulasi fields
                                 if (key.startsWith('t_regulasi.')) {
                                     const fieldName = key.replace('t_regulasi.', '');
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
     });
 </script>