<div class="modal-header">
     <h5 class="modal-title">{{ $title }}</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <div class="card">
       <div class="card-body">
         <table class="table table-borderless">
           <tr>
             <th width="200">Kategori Pintasan</th>
             <td>{{ $detailPintasanLainnya->pintasanLainnya->tpl_nama_kategori ?? 'N/A' }}</td>
           </tr>
           <tr>
             <th width="200">Judul Pintasan</th>
             <td>{{ $detailPintasanLainnya->dpl_judul }}</td>
           </tr>
           <tr>
             <th width="200">URL Pintasan</th>
             <td>
                <a href="{{ $detailPintasanLainnya->dpl_url }}" target="_blank">{{ $detailPintasanLainnya->dpl_url }}</a>
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($detailPintasanLainnya->created_at)) }}</td>
           </tr>
           <tr>
               <th>Dibuat Oleh</th>
               <td>{{ $detailPintasanLainnya->created_by ?? '-' }}</td>
           </tr>
           @if($detailPintasanLainnya->updated_by ?? null)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($detailPintasanLainnya->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $detailPintasanLainnya->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
   </div>