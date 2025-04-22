<!-- views/AdminWeb/AksesCepat/delete.blade.php -->

<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Akses Cepat</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <div class="modal-body">    
     <div class="alert alert-danger mt-3">
       <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus akses cepat dengan detail berikut:
     </div>
     
     <div class="card">
       <div class="card-body">
         <table class="table table-borderless">
           <tr>
             <th width="200">Kategori Akses Cepat</th>
             <td>{{ $aksesCepat->kategoriAkses ? $aksesCepat->kategoriAkses->mka_judul_kategori : '-' }}</td>
           </tr>
           <tr>
             <th>Judul Akses Cepat</th>
             <td>{{ $aksesCepat->ac_judul }}</td>
           </tr>
           <tr>
             <th>URL Akses Cepat</th>
             <td>{{ $aksesCepat->ac_url ?: '-' }}</td>
           </tr>
           <tr>
             <th>Icon Statis</th>
             <td>
               @if($aksesCepat->ac_static_icon)
               <img src="{{ asset('storage/akses_cepat_static_icons/' . basename($aksesCepat->ac_static_icon)) }}"
                      alt="Icon Statis" class="img-thumbnail" style="height: 50px;">
               @else
                 -
               @endif
             </td>
           </tr>
           <tr>
             <th>Icon Animasi</th>
             <td>
               @if($aksesCepat->ac_animation_icon)
               <img src="{{ asset('storage/akses_cepat_animation_icons/' . basename($aksesCepat->ac_animation_icon)) }}" 
                      alt="Icon Animasi" class="img-thumbnail" style="height: 50px;">
               @else
                 -
               @endif
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($aksesCepat->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $aksesCepat->created_by }}</td>
           </tr>
           @if($aksesCepat->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($aksesCepat->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $aksesCepat->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   </div>
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
       onclick="confirmDelete('{{ url('adminweb/akses-cepat/deleteData/'.$aksesCepat->akses_cepat_id) }}')">
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