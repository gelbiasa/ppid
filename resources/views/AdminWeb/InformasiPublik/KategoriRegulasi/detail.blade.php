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
             <th width="200">Regulasi Dinamis</th>
             <td>{{ $kategoriRegulasi->RegulasiDinamis->rd_judul_reg_dinamis ?? 'Tidak ada' }}</td>
           </tr>
           <tr>
             <th>Kode Kategori Regulasi</th>
             <td>{{ $kategoriRegulasi->kr_kategori_reg_kode }}</td>
           </tr>
           <tr>
             <th>Nama Kategori Regulasi</th>
             <td>{{ $kategoriRegulasi->kr_nama_kategori }}</td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($kategoriRegulasi->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $kategoriRegulasi->created_by }}</td>
           </tr>
           @if($kategoriRegulasi->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($kategoriRegulasi->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $kategoriRegulasi->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>