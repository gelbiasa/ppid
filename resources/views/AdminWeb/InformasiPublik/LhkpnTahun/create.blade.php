<div class="modal-header">
     <h5 class="modal-title">Tambah Data Tahun Lhkpn </h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
     </button>
</div>

<div class="modal-body"> 
     <form id="form-create-lhkpn" action="{{url('adminweb/informasipublik/lhkpn-tahun/createData')}}" method="POST"
          enctype="multipart/form-data">
          @csrf
          <div class="form-group">
              <label for="lhkpn_tahun">Tahun LHKPN <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lhkpn_tahun" name="m_lhkpn[lhkpn_tahun]" maxlength="4" placeholder="Masukkan tahun LHKPN">
              <div class="invalid-feedback" id="lhkpn_tahun_error"></div>
              <small class="form-text text-muted">Contoh: 2023</small>
          </div>

          <div class="form-group">
              <label for="lhkpn_judul_informasi">Judul Informasi <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lhkpn_judul_informasi" name="m_lhkpn[lhkpn_judul_informasi]" maxlength="255" placeholder="Masukkan judul informasi">
              <div class="invalid-feedback" id="lhkpn_judul_informasi_error"></div>
          </div>

          <div class="form-group">
              <label for="lhkpn_deskripsi_informasi">Deskripsi Informasi <span class="text-danger">*</span></label>
              <textarea class="form-control" id="lhkpn_deskripsi_informasi" name="m_lhkpn[lhkpn_deskripsi_informasi]" rows="4" placeholder="Masukkan deskripsi informasi"></textarea>
              <div class="invalid-feedback" id="lhkpn_deskripsi_informasi_error"></div>
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
    // Inisialisasi Summernote pada textarea deskripsi informasi
    $('#lhkpn_deskripsi_informasi').summernote({
      placeholder: 'Masukkan deskripsi informasi...',
      tabsize: 2,
      height: 200,
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'italic', 'clear', 'fontsize', 'fontname']], 
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph', 'height', 'align']], 
        ['table', ['table']],
        ['insert', ['link', 'picture']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ],
      callbacks: {
        onChange: function(contents) {
          // Reset invalid state saat konten berubah
          $(this).next('.note-editor').removeClass('is-invalid');
          $('#lhkpn_deskripsi_informasi_error').html('');
        }
      }
    });

    // Tambahkan CSS untuk validasi error pada summernote
    $('<style>.note-editor.is-invalid {border: 1px solid #dc3545 !important;}</style>').appendTo('head');

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
      
      const form = $('#form-create-lhkpn');
      const formData = new FormData(form[0]);
      const button = $(this);
      
      // Tambahkan konten Summernote ke formData
      let summernoteContent = $('#lhkpn_deskripsi_informasi').summernote('code');
      formData.set('m_lhkpn[lhkpn_deskripsi_informasi]', summernoteContent);
      
      // Tampilkan loading state pada tombol submit
      button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
      
      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            $('#myModal').modal('hide');
            
            // Reload tabel data
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
                // Untuk m_lhkpn fields
                if (key.startsWith('m_lhkpn.')) {
                  const fieldName = key.replace('m_lhkpn.', '');
                  
                  // Penanganan khusus untuk deskripsi informasi (Summernote)
                  if (fieldName === 'lhkpn_deskripsi_informasi') {
                    $('#lhkpn_deskripsi_informasi').next('.note-editor').addClass('is-invalid');
                    $('#lhkpn_deskripsi_informasi_error').html(value[0]);
                  } else {
                    $(`#${fieldName}`).addClass('is-invalid');
                    $(`#${fieldName}_error`).html(value[0]);
                  }
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
  });
</script>