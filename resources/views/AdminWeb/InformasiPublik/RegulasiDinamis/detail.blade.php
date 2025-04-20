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
             <th width="200">Judul Regulasi Dinamis</th>
             <td>{{ $RegulasiDinamis->rd_judul_reg_dinamis }}</td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($RegulasiDinamis->created_at)) }}</td>
           </tr>
           <tr>
               <th>Dibuat Oleh</th>
               <td>{{ $RegulasiDinamis->created_by ?? '-' }}</td>
           </tr>
           @if($RegulasiDinamis->updated_by ?? null)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($RegulasiDinamis->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $RegulasiDinamis->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
   </div>