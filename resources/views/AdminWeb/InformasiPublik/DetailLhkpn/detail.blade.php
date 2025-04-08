<div class="modal-header">
     <h5 class="modal-title">{{ $title }}</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <div class="table-responsive">
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
         <tr>
          <th>File LHKPN</th>
          <td>
            @if ($detailLhkpn->dl_file_lhkpn)
              <a href="{{ Storage::url($detailLhkpn->dl_file_lhkpn) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-file-pdf"></i> Lihat File
              </a>
            @else
              <span class="text-muted">Tidak ada file</span>
            @endif
          </td>
        </tr>
         <tr>
           <th>Tanggal Dibuat</th>
           <td>{{ date('d-m-Y H:i:s', strtotime($detailLhkpn->created_at)) }}</td>
         </tr>
         <tr>
           <th>Terakhir Diupdate</th>
           <td>{{ date('d-m-Y H:i:s', strtotime($detailLhkpn->updated_at)) }}</td>
         </tr>
       </table>
     </div>
   </div>
   
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<style>
    .content-preview {
        max-height: 500px;
        overflow-y: auto;
        background-color: #fff;
    }
    .content-preview img {
        max-width: 100%;
        height: auto;
    }
    .table th {
        font-weight: 600;
        color: #555;
    }
</style>