<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Berita</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 <div class="modal-body">    
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Menghapus berita ini akan menghapus seluruh data terkait.
         Apakah Anda yakin ingin menghapus berita dengan detail berikut:
     </div>
     
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID Berita</th>
                     <td>{{ $berita->berita_id }}</td>
                 </tr>
                 <tr>
                     <th>Kategori</th>
                     <td>{{ $berita->BeritaDinamis->bd_nama_submenu ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Tipe Berita</th>
                     <td>
                         <span class="badge badge-{{ $berita->berita_type == 'file' ? 'primary' : 'success' }}">
                             {{ ucfirst($berita->berita_type) }}
                         </span>
                     </td>
                 </tr>
                 @if($berita->berita_type == 'file')
                     <tr>
                         <th>Judul Berita</th>
                         <td>{{ $berita->berita_judul }}</td>
                     </tr>
                 @else
                     <tr>
                         <th>Link Berita</th>
                         <td>
                             <a href="{{ $berita->berita_link }}" target="_blank">
                                 {{ $berita->berita_link }}
                             </a>
                         </td>
                     </tr>
                 @endif
                 <tr>
                     <th>Status Berita</th>
                     <td>
                         <span class="badge badge-{{ $berita->status_berita == 'aktif' ? 'success' : 'danger' }}">
                             {{ ucfirst($berita->status_berita) }}
                         </span>
                     </td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($berita->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $berita->created_by }}</td>
                 </tr>
             </table>
         </div>
     </div>
 </div>
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
         onclick="confirmDelete('{{ url('adminweb/upload-berita/deleteData/'.$berita->berita_id) }}')">
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