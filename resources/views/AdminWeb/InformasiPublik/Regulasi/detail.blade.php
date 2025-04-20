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
             <th width="200">Kategori Regulasi</th>
             <td>{{ $regulasi->KategoriRegulasi->kr_nama_kategori }} ({{ $regulasi->KategoriRegulasi->kr_kategori_reg_kode }})</td>
           </tr>
           <tr>
             <th>Judul</th>
             <td>{{ $regulasi->reg_judul }}</td>
           </tr>
           <tr>
             <th>Sinopsis</th>
             <td>{{ $regulasi->reg_sinopsis }}</td>
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
            <th>Dokumen</th>
            <td>
              @if($regulasi->reg_tipe_dokumen == 'file')
                <a href="{{ Storage::url($regulasi->reg_dokumen) }}" target="_blank" class="btn btn-sm btn-primary">
                  <i class="fas fa-file-pdf"></i> Lihat Dokumen
                </a>
              @else
                <a href="{{ $regulasi->reg_dokumen }}" target="_blank" class="btn btn-sm btn-info">
                  <i class="fas fa-external-link-alt"></i> Buka Link
                </a>
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
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
     </button>
   </div>