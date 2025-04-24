@php
  use App\Models\Website\WebMenuModel;
  $detailFooterUrl = WebMenuModel::getDynamicMenuUrl('detail-footer');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Footer</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">    
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus footer dengan detail berikut:
  </div>
  
  <div class="card">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Kategori Footer</th>
          <td>{{ $footer->kategoriFooter->kt_footer_nama ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
          <th>Judul Footer</th>
          <td>{{ $footer->f_judul_footer }}</td>
        </tr>
        <tr>
          <th>URL Footer</th>
          <td>
              @if($footer->f_url_footer)
                  <a href="{{ $footer->f_url_footer }}" target="_blank">{{ $footer->f_url_footer }}</a>
              @else
                  -
              @endif
          </td>
        </tr>
        <tr>
          <th>Ikon Footer</th>
          <td>
            @if($footer->f_icon_footer)
            <img src="{{ asset('storage/footer_icons/' . basename($footer->f_icon_footer)) }}" 
                 alt="{{ $footer->f_judul_footer }}" 
                 style="max-width: 100px; max-height: 100px;">
            <br>
            <small>{{ basename($footer->f_icon_footer) }}</small>
        @else
            Tidak ada ikon
        @endif
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="alert alert-warning mt-3">
    <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus footer ini akan menghapus data secara permanen. 
    Pastikan Anda yakin ingin melanjutkan.
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
  onclick="confirmDelete('{{ url( $detailFooterUrl . '/deleteData/' . $footer->footer_id) }}')">
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