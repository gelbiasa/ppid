@php
  use App\Models\Website\WebMenuModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Ubah Pengguna</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formUpdateUser" action="{{ url($managementUserUrl . '/updateData/' . $user->user_id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nama_pengguna" name="m_user[nama_pengguna]" maxlength="50" value="{{ $user->nama_pengguna }}">
      <div class="invalid-feedback" id="nama_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="email_pengguna">Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email_pengguna" name="m_user[email_pengguna]" maxlength="255" value="{{ $user->email_pengguna }}">
      <div class="invalid-feedback" id="email_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="no_hp_pengguna">Nomor HP <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" maxlength="15" value="{{ $user->no_hp_pengguna }}">
      <div class="invalid-feedback" id="no_hp_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="nik_pengguna">NIK <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nik_pengguna" name="m_user[nik_pengguna]" maxlength="16" value="{{ $user->nik_pengguna }}">
      <div class="invalid-feedback" id="nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="alamat_pengguna">Alamat <span class="text-danger">*</span></label>
      <textarea class="form-control" id="alamat_pengguna" name="m_user[alamat_pengguna]" rows="3">{{ $user->alamat_pengguna }}</textarea>
      <div class="invalid-feedback" id="alamat_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="pekerjaan_pengguna">Pekerjaan <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" value="{{ $user->pekerjaan_pengguna }}">
      <div class="invalid-feedback" id="pekerjaan_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="upload_nik_pengguna">Upload KTP <small>(biarkan kosong jika tidak ingin mengubah)</small></label>
      @if($user->upload_nik_pengguna)
        <div class="mb-2">
          <a href="{{ asset('storage/' . $user->upload_nik_pengguna) }}" target="_blank" class="btn btn-sm btn-info">
            <i class="fas fa-eye"></i> Lihat KTP Saat Ini
          </a>
        </div>
      @endif
      <input type="file" class="form-control-file" id="upload_nik_pengguna" name="upload_nik_pengguna">
      <small class="form-text text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
      <div class="invalid-feedback" id="upload_nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="password">Password <small>(biarkan kosong jika tidak ingin mengubah)</small></label>
      <input type="password" class="form-control" id="password" name="password">
      <div class="invalid-feedback" id="password_error"></div>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Konfirmasi Password</label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
      <div class="invalid-feedback" id="password_confirmation_error"></div>
    </div>

    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Hak Akses</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Level</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($user->hakAkses as $hakAkses)
              <tr>
                <td>{{ $hakAkses->hak_akses_kode }}</td>
                <td>{{ $hakAkses->hak_akses_nama }}</td>
                <td>
                  @if(count($user->hakAkses) > 1)
                    @if(Auth::user()->level->hak_akses_kode === 'SAR' || $hakAkses->hak_akses_kode !== 'SAR')
                      <button type="button" class="btn btn-sm btn-danger delete-hak-akses" 
                        data-id="{{ $hakAkses->pivot->set_user_hak_akses_id }}" 
                        data-name="{{ $hakAkses->hak_akses_nama }}">
                        <i class="fas fa-trash"></i> Hapus
                      </button>
                    @endif
                  @else
                    <span class="text-muted">Minimal 1 hak akses</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">Tidak ada hak akses</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if(count($availableHakAkses) > 0)
          <div class="form-group mt-3">
            <label for="add_hak_akses">Tambah Hak Akses</label>
            <div class="input-group">
              <select class="form-control" id="add_hak_akses">
                <option value="">-- Pilih Hak Akses --</option>
                @foreach($availableHakAkses as $hakAkses)
                  <option value="{{ $hakAkses->hak_akses_id }}">{{ $hakAkses->hak_akses_nama }}</option>
                @endforeach
              </select>
              <div class="input-group-append">
                <button class="btn btn-success" type="button" id="btnAddHakAkses">
                  <i class="fas fa-plus"></i> Tambah
                </button>
              </div>
            </div>
          </div>
        @endif
      </div>
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
      
      const form = $('#formUpdateUser');
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
          button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
        }
      });
    });

    // Handle hapus hak akses
    $(document).on('click', '.delete-hak-akses', function() {
      const id = $(this).data('id');
      const name = $(this).data('name');
      
      Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus hak akses ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ url($managementUserUrl) }}' + '/deleteHakAkses/' + id,
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              if (response.success) {
                // Muat ulang modal
                modalAction('{{ url($managementUserUrl . "/editData/" . $user->user_id) }}');
                
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil',
                  text: response.message
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: response.message || 'Terjadi kesalahan saat menghapus hak akses'
                });
              }
            },
            error: function(xhr) {
              let errorMessage = 'Terjadi kesalahan saat menghapus hak akses. Silakan coba lagi.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
              }
              
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorMessage
              });
            }
          });
        }
      });
    });

    // Handle tambah hak akses
    $('#btnAddHakAkses').on('click', function() {
      const hakAksesId = $('#add_hak_akses').val();
      if (!hakAksesId) {
        Swal.fire({
          icon: 'warning',
          title: 'Perhatian',
          text: 'Pilih hak akses terlebih dahulu'
        });
        return;
      }
      
      $.ajax({
        url: '{{ url($managementUserUrl . "/addHakAkses/" . $user->user_id) }}',
        type: 'POST',
        data: {
          hak_akses_id: hakAksesId
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            // Muat ulang modal
            modalAction('{{ url($managementUserUrl . "/editData/" . $user->user_id) }}');
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message || 'Terjadi kesalahan saat menambahkan hak akses'
            });
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menambahkan hak akses. Silakan coba lagi.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
          });
        }
      });
    });
  });
</script>