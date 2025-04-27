@php
  use App\Models\Website\WebMenuModel;
  $regulasiDinamisUrl = WebMenuModel::getDynamicMenuUrl('detail-regulasi');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Regulasi</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">    
     <div class="alert alert-danger mt-3">
       <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus regulasi dengan detail berikut:
     </div>
     
     <div class="card">
       <div class="card-body">
         <table class="table table-borderless">
           <tr>
             <th width="200">Kategori Regulasi</th>
             <td>{{ $regulasi->KategoriRegulasi->kr_nama_kategori }} ({{ $regulasi->KategoriRegulasi->kr_kategori_reg_kode }})</td>
           </tr>
           <tr>
             <th>Judul</th>
             <td>{{ $regulasi->reg_judul }}</td>
           </tr>
           <tr>
             <th>Tipe Dokumen</th>
             <td>
               @if($regulasi->reg_tipe_dokumen == 'file')
                 <span class="badge badge-primary">File</span>
               @else
                 <span class="badge badge-info">Link</span>
               @endif
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($regulasi->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $regulasi->created_by }}</td>
           </tr>
           @if($regulasi->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($regulasi->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $regulasi->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   
     <div class="alert alert-warning mt-3">
       <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
       Menghapus regulasi ini akan menghapus seluruh data terkait secara permanen.
     </div>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
       onclick="confirmDelete('{{ url( $regulasiDinamisUrl . '/deleteData/' . $regulasi->regulasi_id) }}')">
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