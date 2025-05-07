@php
  use App\Models\Website\WebMenuModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Tambah Pengguna Baru</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateUser" action="{{ url($managementUserUrl . '/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nama_pengguna" name="m_user[nama_pengguna]" maxlength="50">
      <div class="invalid-feedback" id="nama_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="email_pengguna">Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email_pengguna" name="m_user[email_pengguna]" maxlength="255">
      <div class="invalid-feedback" id="email_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="no_hp_pengguna">Nomor HP <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" maxlength="15">
      <div class="invalid-feedback" id="no_hp_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="nik_pengguna">NIK <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nik_pengguna" name="m_user[nik_pengguna]" maxlength="16">
      <div class="invalid-feedback" id="nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="alamat_pengguna">Alamat <span class="text-danger">*</span></label>
      <textarea class="form-control" id="alamat_pengguna" name="m_user[alamat_pengguna]" rows="3"></textarea>
      <div class="invalid-feedback" id="alamat_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="pekerjaan_pengguna">Pekerjaan <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]">
      <div class="invalid-feedback" id="pekerjaan_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="upload_nik_pengguna">Upload KTP <span class="text-danger">*</span></label>
      <input type="file" class="form-control-file" id="upload_nik_pengguna" name="upload_nik_pengguna">
      <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
      <div class="invalid-feedback" id="upload_nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="hak_akses_id">Level Pengguna <span class="text-danger">*</span></label>
      <select class="form-control" id="hak_akses_id" name="hak_akses_id">
        <option value="">-- Pilih Level --</option>
        @foreach($hakAkses as $hak)
          <option value="{{ $hak->hak_akses_id }}" {{ isset($selectedLevel) && $selectedLevel->hak_akses_id == $hak->hak_akses_id ? 'selected' : '' }}>
            {{ $hak->hak_akses_nama }}
          </option>
        @endforeach
      </select>
      <div class="invalid-feedback" id="hak_akses_id_error"></div>
    </div>

    <div class="form-group">
      <label for="password">Password <span class="text-danger">*</span></label>
      <input type="password" class="form-control" id="password" name="password">
      <div class="invalid-feedback" id="password_error"></div>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
      <div class="invalid-feedback" id="password_confirmation_error"></div>
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
      
      const form = $('#formCreateUser');
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
                // Untuk m_user fields
                if (key.startsWith('m_user.')) {
                  const fieldName = key.replace('m_user.', '');
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
          let errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
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