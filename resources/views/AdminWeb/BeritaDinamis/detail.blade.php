<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">Detail Berita Dinamis</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID Berita Dinamis</th>
                     <td>{{ $beritaDinamis->berita_dinamis_id }}</td>
                 </tr>
                 <tr>
                     <th>Nama Submenu</th>
                     <td>{{ $beritaDinamis->bd_nama_submenu }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $beritaDinamis->created_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Pada</th>
                     <td>{{ $beritaDinamis->created_at ? date('d-m-Y H:i:s', strtotime($beritaDinamis->created_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $beritaDinamis->updated_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $beritaDinamis->updated_at ? date('d-m-Y H:i:s', strtotime($beritaDinamis->updated_at)) : '-' }}</td>
                 </tr>
             </table>
         </div>
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>