<!-- views/SistemInformasi/Timeline/create.blade.php -->
<div class="modal-header">
  <h5 class="modal-title">Tambah Timeline Baru</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateTimeline" action="{{ url('SistemInformasi/Timeline/createData') }}" method="POST">
    @csrf

    <div class="form-group">
      <label for="kategori_form">Kategori Form <span class="text-danger">*</span></label>
      <select class="form-control" id="kategori_form" name="t_timeline[fk_m_kategori_form]">
        <option value="">-- Pilih Kategori --</option>
        @foreach($TimelineKategoriForm as $kategori)
          <option value="{{ $kategori->kategori_form_id }}">{{ $kategori->kf_nama }}</option>
        @endforeach
      </select>
      <div class="invalid-feedback" id="kategori_form_error"></div>
    </div>

    <div class="form-group">
      <label for="judul_timeline">Judul Timeline <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="judul_timeline" name="t_timeline[judul_timeline]" maxlength="255">
      <div class="invalid-feedback" id="judul_timeline_error"></div>
    </div>

    <div class="form-group">
      <label for="jumlah_langkah_timeline">Jumlah Langkah Timeline <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="jumlah_langkah_timeline" name="jumlah_langkah_timeline"
        min="1" max="20" placeholder="Masukkan jumlah langkah (1-20)">
      <div class="invalid-feedback" id="jumlah_langkah_timeline_error"></div>
      <small class="form-text text-muted">Minimal 1, maksimal 20 langkah</small>
    </div>

    <div id="langkah_container">
      <!-- Input langkah timeline akan muncul di sini secara dinamis setelah jumlah langkah diisi -->
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
  function generateLangkahFields() {
    const jumlahLangkah = parseInt($('#jumlah_langkah_timeline').val());
    const container = $('#langkah_container');

    if (isNaN(jumlahLangkah) || jumlahLangkah < 1 || jumlahLangkah > 20) {
      container.empty();
      return;
    }

    // Clear existing fields
    container.empty();

    // Generate new fields
    for (let i = 1; i <= jumlahLangkah; i++) {
      const field = `
        <div class="form-group">
          <label for="langkah_timeline_${i}">
            Langkah Timeline ${i} <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <input type="text" class="form-control" id="langkah_timeline_${i}" 
              name="langkah_timeline_${i}" maxlength="255">
            <div class="input-group-append">
              <span class="input-group-text bg-light">
                <i class="fas fa-sort"></i>
              </span>
            </div>
          </div>
          <div class="invalid-feedback" id="langkah_timeline_${i}_error"></div>
        </div>
      `;
      container.append(field);
    }
  }

  $(document).ready(function () {
    // Handle input event pada field jumlah langkah
    $('#jumlah_langkah_timeline').on('input', function () {
      if ($(this).val() !== '') {
        generateLangkahFields();
      } else {
        $('#langkah_container').empty();
      }
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
      
      const form = $('#formCreateTimeline');
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
            $('#table_timeline').DataTable().ajax.reload();
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            if (response.errors) {
              // Tampilkan pesan error pada masing-masing field
              $.each(response.errors, function(key, value) {
                // Untuk t_timeline fields
                if (key.startsWith('t_timeline.')) {
                  const fieldName = key.replace('t_timeline.', '');
                  if (fieldName === 'fk_m_kategori_form') {
                    $('#kategori_form').addClass('is-invalid');
                    $('#kategori_form_error').html(value[0]);
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