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
     <button type="button" class="btn btn-danger" id="btnConfirmDelete">
       <i class="fas fa-trash mr-1"></i> Hapus
     </button>
   </div>
   
   <script>
     $(document).ready(function() {
       $('#btnConfirmDelete').on('click', function() {
         const button = $(this);
         
         // Tampilkan loading state pada tombol
         button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').attr('disabled', true);
         
         $.ajax({
           url: '{{ url("adminweb/informasipublik/detail-lhkpn/deleteData/{$detailLhkpn->detail_lhkpn_id}") }}',
           type: 'DELETE',
           headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           success: function(response) {
             if (response.success) {
               $('#myModal').modal('hide');
               
               // Reload tabel
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
                 text: response.message || 'Terjadi kesalahan saat menghapus data'
               });
             }
           },
           error: function(xhr) {
             console.error('Error:', xhr);
             Swal.fire({
               icon: 'error',
               title: 'Gagal',
               text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
             });
           },
           complete: function() {
             // Kembalikan tombol ke keadaan semula
             button.html('<i class="fas fa-trash mr-1"></i> Hapus').attr('disabled', false);
           }
         });
       });
     });
   </script>