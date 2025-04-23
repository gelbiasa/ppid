<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Detail Pintasan Lainnya</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus detail pintasan lainnya dengan detail
         berikut:
     </div>
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Kategori Pintasan</th>
                     <td>{{ $detailPintasanLainnya->pintasanLainnya->tpl_nama_kategori ?? 'N/A' }}</td>
                 </tr>
                 <tr>
                     <th width="200">Judul Pintasan</th>
                     <td>{{ $detailPintasanLainnya->dpl_judul }}</td>
                 </tr>
                 <tr>
                     <th width="200">URL Pintasan</th>
                     <td>
                         <a href="{{ $detailPintasanLainnya->dpl_url }}" target="_blank">{{ $detailPintasanLainnya->dpl_url }}</a>
                     </td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($detailPintasanLainnya->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $detailPintasanLainnya->created_by ?? '-' }}</td>
                 </tr>
                 @if ($detailPintasanLainnya->updated_by ?? null)
                     <tr>
                         <th>Terakhir Diperbarui</th>
                         <td>{{ date('d-m-Y H:i:s', strtotime($detailPintasanLainnya->updated_at)) }}</td>
                     </tr>
                     <tr>
                         <th>Diperbarui Oleh</th>
                         <td>{{ $detailPintasanLainnya->updated_by }}</td>
                     </tr>
                 @endif
             </table>
         </div>
     </div>
     <div class="alert alert-warning mt-3">
      <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus detail pintasan ini akan menghapus seluruh data terkait secara permanen..
    </div>
 </div>
 
 <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
        onclick="confirmDelete('{{ url('adminweb/DetailPintasanLainnya/deleteData/'.$detailPintasanLainnya->detail_pintasan_lainnya_id) }}')">
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