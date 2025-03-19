<!-- views/AdminWeb/Footer/delete.blade.php -->

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
             <td>{{ $footer->kategoriFooter ? $footer->kategoriFooter->kt_footer_nama : '-' }}</td>
           </tr>
           <tr>
             <th>Judul Footer</th>
             <td>{{ $footer->f_judul_footer }}</td>
           </tr>
           <tr>
             <th>URL Footer</th>
             <td>{{ $footer->f_url_footer ?: '-' }}</td>
           </tr>
           <tr>
             <th>Icon Footer</th>
             <td>
               @if($footer->f_icon_footer)
                 <img src="{{ asset('storage/' . $footer::ICON_PATH . '/' . $footer->f_icon_footer) }}" 
                      alt="Icon Footer" class="img-thumbnail" style="height: 80px;">
               @else
                 -
               @endif
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($footer->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $footer->created_by }}</td>
           </tr>
           @if($footer->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($footer->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $footer->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   </div>
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
       onclick="confirmDelete('{{ url('adminweb/footer/deleteData/'.$footer->footer_id) }}')">
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