@php
  use App\Models\Website\WebMenuModel;
  $managementLevelUrl = WebMenuModel::getDynamicMenuUrl('management-level');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Konfirmasi Hapus Level</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
</div>
  
<div class="modal-body">    
    <div class="alert alert-danger mt-3">
      <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus level dengan detail berikut:
    </div>
    
    <div class="card">
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Kode Level</th>
            <td>{{ $level->level_kode }}</td>
          </tr>
          <tr>
            <th width="200">Nama Level</th>
            <td>{{ $level->level_nama }}</td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($level->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $level->created_by }}</td>
          </tr>
          @if($level->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($level->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $level->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  
    <div class="alert alert-warning mt-3">
      <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus level ini mungkin akan memengaruhi data pengguna atau data lain yang terkait dengannya. Pastikan tidak ada pengguna atau data lain yang masih menggunakan level ini.
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
                url: '{{ url($managementLevelUrl . "/deleteData/" . $level->level_id) }}',
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
        });
    });
</script>