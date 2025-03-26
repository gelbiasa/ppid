<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">Detail Media Dinamis</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID Media Dinamis</th>
                     <td>{{ $mediaDinamis->media_dinamis_id }}</td>
                 </tr>
                 <tr>
                     <th>Kategori Media</th>
                     <td>{{ $mediaDinamis->md_kategori_media }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $mediaDinamis->created_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Pada</th>
                     <td>{{ $mediaDinamis->created_at ? date('d-m-Y H:i:s', strtotime($mediaDinamis->created_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $mediaDinamis->updated_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $mediaDinamis->updated_at ? date('d-m-Y H:i:s', strtotime($mediaDinamis->updated_at)) : '-' }}</td>
                 </tr>
             </table>
         </div>
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>