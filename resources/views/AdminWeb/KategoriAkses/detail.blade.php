<!-- views/AdminWeb/KategoriAkses/detail.blade.php -->

<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">Detail Kategori Akses</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
  
 <div class="modal-body">
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID Kategori Akses</th>
                     <td>{{ $kategoriAkses->kategori_akses_id }}</td>
                 </tr>
                 <tr>
                     <th>Judul Kategori</th>
                     <td>{{ $kategoriAkses->mka_judul_kategori }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $kategoriAkses->created_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Pada</th>
                     <td>{{ $kategoriAkses->created_at ? date('d-m-Y H:i:s', strtotime($kategoriAkses->created_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $kategoriAkses->updated_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $kategoriAkses->updated_at ? date('d-m-Y H:i:s', strtotime($kategoriAkses->updated_at)) : '-' }}</td>
                 </tr>
             </table>
         </div>
     </div>
 </div>
  
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>