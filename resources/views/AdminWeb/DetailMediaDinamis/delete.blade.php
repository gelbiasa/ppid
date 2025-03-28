<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Media Dinamis</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">    
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus media dinamis dengan detail berikut:
     </div>
     
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Kategori Media</th>
                     <td>{{ optional($detailMediaDinamis->mediaDinamis)->md_kategori_media }}</td>
                 </tr>
                 <tr>
                     <th>Judul Media</th>
                     <td>{{ $detailMediaDinamis->dm_judul_media }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($detailMediaDinamis->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $detailMediaDinamis->created_by }}</td>
                 </tr>
                 @if($detailMediaDinamis->updated_by)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($detailMediaDinamis->updated_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $detailMediaDinamis->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>
 
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
         Menghapus media dinamis ini mungkin akan memengaruhi data lain yang terkait dengannya. 
         Pastikan tidak ada data lain yang masih menggunakan media dinamis ini.
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
     onclick="confirmDelete('{{ url('adminweb/media-detail/deleteData/'.$detailMediaDinamis->detail_media_dinamis_id) }}')">
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