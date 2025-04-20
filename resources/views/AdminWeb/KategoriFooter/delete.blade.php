<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Kategori Footer</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">    
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus kategori footer dengan detail berikut:
  </div>
  
  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Kode Kategori Footer</th>
          <td>{{ $kategoriFooter->kt_footer_kode }}</td>
        </tr>
        <tr>
          <th>Nama Kategori Footer</th>
          <td>{{ $kategoriFooter->kt_footer_nama }}</td>
        </tr>
        <tr>
          <th>Tanggal Dibuat</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($kategoriFooter->created_at)) }}</td>
        </tr>
        <tr>
          <th>Dibuat Oleh</th>
          <td>{{ $kategoriFooter->created_by }}</td>
        </tr>
        @if($kategoriFooter->updated_by)
        <tr>
          <th>Terakhir Diperbarui</th>
          <td>{{ date('d-m-Y H:i:s', strtotime($kategoriFooter->updated_at)) }}</td>
        </tr>
        <tr>
          <th>Diperbarui Oleh</th>
          <td>{{ $kategoriFooter->updated_by }}</td>
        </tr>
        @endif
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus kategori footer ini mungkin akan memengaruhi data footer yang terkait dengannya. Pastikan tidak ada data footer yang masih menggunakan kategori footer ini.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
    onclick="confirmDelete('{{ url('adminweb/kategori-footer/deleteData/'.$kategoriFooter->kategori_footer_id) }}')">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<script>
  function confirmDelete(url) {
    const button = $('#confirmDeleteButton');
    
    button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
    
    $.ajax({
      url: url,
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
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
        });
        
        button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
      }
    });
  }
</script>