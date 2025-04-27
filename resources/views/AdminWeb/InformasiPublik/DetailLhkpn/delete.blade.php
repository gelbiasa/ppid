@php
  use App\Models\Website\WebMenuModel;
  $detailLHKPNUrl = WebMenuModel::getDynamicMenuUrl('detail-lhkpn');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <div class="alert alert-danger">
       <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus data ini?
     </div>
     
     <table class="table table-bordered">
       <tr>
         <th width="30%">Tahun LHKPN</th>
         <td>{{ $detailLhkpn->lhkpn->lhkpn_tahun }}</td>
       </tr>
       <tr>
         <th>Judul Informasi</th>
         <td>{{ $detailLhkpn->lhkpn->lhkpn_judul_informasi }}</td>
       </tr>
       <tr>
         <th>Nama Karyawan</th>
         <td>{{ $detailLhkpn->dl_nama_karyawan }}</td>
       </tr>
     </table>
   
     <div class="alert alert-warning mt-3">
      <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
      Data yang sudah dihapus tidak dapat dikembalikan!
  </div>
   </div>

   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="btnConfirmDelete"
        onclick="confirmDelete('{{ url( $detailLHKPNUrl . '/deleteData/' . $detailLhkpn->detail_lhkpn_id) }}')">
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
      success: function (response) {
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
      error: function (xhr) {
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