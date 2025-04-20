<div class="modal-header">
     <h5 class="modal-title">Detail Pintasan Lainnya</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
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
                 @if($pintasanLainnya->updated_by)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($pintasanLainnya->updated_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $pintasanLainnya->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>