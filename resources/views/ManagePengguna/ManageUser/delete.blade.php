@php
  use App\Models\Website\WebMenuModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Pengguna</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">    
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus pengguna dengan detail berikut:
  </div>
  
  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Nama Pengguna</th>
          <td>{{ $user->nama_pengguna }}</td>
        </tr>
        <tr>
          <th>Email</th>
          <td>{{ $user->email_pengguna }}</td>
        </tr>
        <tr>
          <th>Nomor HP</th>
          <td>{{ $user->no_hp_pengguna }}</td>
        </tr>
        <tr>
          <th>NIK</th>
          <td>{{ $user->nik_pengguna }}</td>
        </tr>
      </table>

      <h5 class="mt-3">Hak Akses:</h5>
      <ul>
        @foreach($user->hakAkses as $hakAkses)
          <li>{{ $hakAkses->hak_akses_nama }} ({{ $hakAkses->hak_akses_kode }})</li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus pengguna ini akan menghapus semua data terkait dengan pengguna tersebut.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<script>
  $(document).ready(function () {
    $('#confirmDeleteButton').on('click', function() {
      const button = $(this);
      
      button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
      
      $.ajax({
        url: '{{ url($managementUserUrl . "/deleteData/" . $user->user_id) }}',
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#myModal').modal('hide');
          
          if (response.success) {
            reloadTable();
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message
            });
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
          });
          
          button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
        }
      });
    });
  });
</script>