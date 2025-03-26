<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Pengumuman Dinamis</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">    
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus pengumuman dinamis dengan detail berikut:
  </div>
  
  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Nama Submenu Pengumuman</th>
          <td>{{ $pengumumanDinamis->pd_nama_submenu }}</td>
        </tr>
        <tr>
          <th>Tanggal Dibuat</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($pengumumanDinamis->created_at)) }}</td>
        </tr>
        <tr>
          <th>Dibuat Oleh</th>
          <td>{{ $pengumumanDinamis->created_by }}</td>
        </tr>
        @if($pengumumanDinamis->updated_by)
        <tr>
          <th>Terakhir Diperbarui</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($pengumumanDinamis->updated_at)) }}</td>
        </tr>
        <tr>
          <th>Diperbarui Oleh</th>
          <td>{{ $pengumumanDinamis->updated_by }}</td>
        </tr>
        @endif
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
    Menghapus pengumuman dinamis ini akan menghapus submenu pengumuman dari website publik. Semua konten yang terkait dengan pengumuman ini juga mungkin tidak akan dapat diakses lagi.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<script>
  $(document).ready(function() {
    $('#confirmDeleteButton').on('click', function() {
      const button = $(this);
      
      button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
      
      $.ajax({
        url: '{{ url("AdminWeb/PengumumanDinamis/deleteData/".$pengumumanDinamis->pengumuman_dinamis_id) }}',
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#myModal').modal('hide');
          
          if (response.success) {
            // Perbaikan: Gunakan fungsi reloadTable() untuk memuat ulang data
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
          console.error('Error:', xhr);
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
          });
          
          button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
        }
      });
    });
  });
</script>