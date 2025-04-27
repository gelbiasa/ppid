@php
  use App\Models\Website\WebMenuModel;
  $kategoriTahunLHKPNUrl = WebMenuModel::getDynamicMenuUrl('kategori-tahun-lhkpn');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Data LHKPN</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">    
     <div class="alert alert-danger">
         <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus data LHKPN dengan detail berikut:
     </div>
     
     <div class="card">
        <div class="card-header">
            <h5 class="card-title">Informasi LHKPN</h5>
        </div>
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Tahun LHKPN</th>
                     <td>{{ $lhkpn->lhkpn_tahun ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Judul Informasi</th>
                     <td>{{ $lhkpn->lhkpn_judul_informasi ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($lhkpn->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $lhkpn->created_by ?? '-' }}</td>
                 </tr>
                 @if($lhkpn->updated_by ?? null)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($lhkpn->updated_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $lhkpn->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>

     <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Detail Deskripsi Informasi</h5>
        </div>
        <div class="card-body">
            {!! $lhkpn->lhkpn_deskripsi_informasi ?? '<div class="alert alert-info">Tidak ada deskripsi yang tersedia.</div>' !!}
        </div>
    </div>
 
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
         Menghapus data LHKPN ini akan menghapus semua data terkait. 
         Tindakan ini tidak dapat dibatalkan.
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
     onclick="confirmDelete('{{ url( $kategoriTahunLHKPNUrl . '/deleteData/' . $lhkpn->lhkpn_id) }}')">
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