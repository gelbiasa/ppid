@php
  use App\Models\Website\WebMenuModel;
  $kategoriFooterUrl = WebMenuModel::getDynamicMenuUrl('kategori-footer');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah Kategori Footer Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <form id="formCreateKategoriFooter" action="{{ url($kategoriFooterUrl . '/createData') }}" method="POST">
      @csrf
  
      <div class="form-group">
        <label for="kt_footer_kode">Kode Kategori Footer <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="kt_footer_kode" name="m_kategori_footer[kt_footer_kode]" maxlength="20">
        <div class="invalid-feedback" id="kt_footer_kode_error"></div>
      </div>

      <div class="form-group">
        <label for="kt_footer_nama">Nama Kategori Footer <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="kt_footer_nama" name="m_kategori_footer[kt_footer_nama]" maxlength="100">
        <div class="invalid-feedback" id="kt_footer_nama_error"></div>
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
  
      // Handle submit form
      $('#btnSubmitForm').on('click', function() {
        // Reset semua error
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        const form = $('#formCreateKategoriFooter');
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
                  // Untuk m_kategori_footer fields
                  if (key.startsWith('m_kategori_footer.')) {
                    const fieldName = key.replace('m_kategori_footer.', '');
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