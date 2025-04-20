<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Pintasan Lainnya</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">    
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus pintasan lainnya dengan detail berikut:
     </div>
     
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Kategori Akses</th>
                     <td>{{ $pintasanLainnya->kategoriAkses->mka_judul_kategori ?? 'Tidak Tersedia' }}</td>
                 </tr>
                 <tr>
                     <th>Nama Pintasan</th>
                     <td>{{ $pintasanLainnya->tpl_nama_kategori }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($pintasanLainnya->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $pintasanLainnya->created_by ?? 'Sistem' }}</td>
                 </tr>
             </table>
         </div>
     </div>
 
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus pintasan lainnya ini mungkin akan memengaruhi data terkait dengannya. Pastikan tidak ada detail pintasan lainnya yang terkait dengan pintasan lainnya ini.
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
         onclick="confirmDelete('{{ url('adminweb/pintasan-lainnya/deleteData/'.$pintasanLainnya->pintasan_lainnya_id) }}')">
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
                 if (typeof reloadTable === 'function') {
                     reloadTable();
                 }
                 
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