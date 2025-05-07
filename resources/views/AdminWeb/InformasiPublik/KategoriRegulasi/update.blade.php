@php
  use App\Models\Website\WebMenuModel;
  $kategoriRegulasiUrl = WebMenuModel::getDynamicMenuUrl('kategori-regulasi');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Ubah Kategori Regulasi</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <form id="formUpdateKategoriRegulasi" action="{{ url($kategoriRegulasiUrl . '/updateData/' . $kategoriRegulasi->kategori_reg_id) }}" method="POST">
         @csrf
 
         <div class="form-group">
             <label for="fk_regulasi_dinamis">Regulasi Dinamis <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_regulasi_dinamis" name="m_kategori_regulasi[fk_regulasi_dinamis]">
                 <option value="">Pilih Regulasi Dinamis</option>
                 @foreach($regulasiDinamis as $regulasi)
                     <option value="{{ $regulasi->regulasi_dinamis_id }}" 
                         {{ $kategoriRegulasi->fk_regulasi_dinamis == $regulasi->regulasi_dinamis_id ? 'selected' : '' }}>
                         {{ $regulasi->rd_judul_reg_dinamis }}
                     </option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="fk_regulasi_dinamis_error"></div>
         </div>
 
         <div class="form-group">
             <label for="kr_kategori_reg_kode">Kode Kategori Regulasi <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="kr_kategori_reg_kode" name="m_kategori_regulasi[kr_kategori_reg_kode]" maxlength="20" 
             value="{{ $kategoriRegulasi->kr_kategori_reg_kode }}">
             <div class="invalid-feedback" id="kr_kategori_reg_kode_error"></div>
         </div>
 
         <div class="form-group">
             <label for="kr_nama_kategori">Nama Kategori Regulasi <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="kr_nama_kategori" name="m_kategori_regulasi[kr_nama_kategori]" maxlength="200"
             value="{{ $kategoriRegulasi->kr_nama_kategori }}">
             <div class="invalid-feedback" id="kr_nama_kategori_error"></div>
         </div>
     </form>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-primary" id="btnSubmitForm">
         <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
 
         // Handle submit form
         $('#btnSubmitForm').on('click', function() {
             // Reset semua error
             $('.is-invalid').removeClass('is-invalid');
             $('.invalid-feedback').html('');
             
             const form = $('#formUpdateKategoriRegulasi');
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
                                 // Untuk m_kategori_regulasi fields
                                 if (key.startsWith('m_kategori_regulasi.')) {
                                     const fieldName = key.replace('m_kategori_regulasi.', '');
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
                     button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                 }
             });
         });
     });
 </script>