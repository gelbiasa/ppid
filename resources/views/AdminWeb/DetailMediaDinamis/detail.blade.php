<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">{{ $title }}</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
     </button>
</div>

<div class="modal-body">
     <div class="card">
          <div class="card-body">
               <table class="table table-borderless">
                    <tr>
                         <th width="200">Kategori Media Dinamis</th>
                         <td>{{ $detailMedia->MediaDinamis->md_kategori_media ?? '-' }}</td>
                    </tr>
                    <tr>
                         <th>Judul Media</th>
                         <td>{{ $detailMedia->dm_judul_media ?? '-' }}</td>
                    </tr>
                    <tr>
                         <th>Tipe Media</th>
                         <td>
                              @if($detailMedia->dm_type_media == 'file')
                                   <span class="badge badge-primary">File</span>
                              @elseif($detailMedia->dm_type_media == 'link')
                                   <span class="badge badge-info">Link</span>
                              @else
                                   <span class="badge badge-secondary">-</span>
                              @endif
                         </td>
                    </tr>
                    <tr>
                         <th>Status Media</th>
                         <td>
                              @if($detailMedia->status_media == 'aktif')
                                   <span class="badge badge-success">Aktif</span>
                              @else
                                   <span class="badge badge-danger">Nonaktif</span>
                              @endif
                         </td>
                    </tr>
                    <tr>
                         <th>Tanggal Dibuat</th>
                         <td>{{ date('d-m-Y H:i:s', strtotime($detailMedia->created_at)) }}</td>
                    </tr>
                    <tr>
                         <th>Dibuat Oleh</th>
                         <td>{{ $detailMedia->created_by ?? '-' }}</td>
                    </tr>
                    @if($detailMedia->updated_by)
                    <tr>
                         <th>Terakhir Diperbarui</th>
                         <td>{{ date('d-m-Y H:i:s', strtotime($detailMedia->updated_at)) }}</td>
                    </tr>
                    <tr>
                         <th>Diperbarui Oleh</th>
                         <td>{{ $detailMedia->updated_by }}</td>
                    </tr>
                    @endif
               </table>
          </div>
     </div>

     <div class="card mt-3">
          <div class="card-header">
               <h5 class="card-title">Detail Konten Media</h5>
          </div>
          <div class="card-body">
               @if($detailMedia->dm_type_media == 'link')
                    <h6>URL Tujuan:</h6>
                    <div class="mb-3">
                         <a href="{{ $detailMedia->dm_media_upload }}" target="_blank" class="btn btn-info">
                              <i class="fas fa-external-link-alt mr-1"></i> 
                              {{ $detailMedia->dm_media_upload }}
                         </a>
                    </div>
               @elseif($detailMedia->dm_type_media == 'file')
                    <h6>File:</h6>
                    <div class="mb-3">
                         <a href="{{ asset('storage/' . $detailMedia->dm_media_upload) }}" target="_blank" class="btn btn-primary">
                              <i class="fas fa-file-download mr-1"></i> Lihat File
                         </a>
                         <span class="ml-2 text-muted">{{ basename($detailMedia->dm_media_upload) }}</span>
                    </div>
               @else
                    <div class="alert alert-info">
                         Tidak ada detail konten yang tersedia.
                    </div>
               @endif
          </div>
     </div>
</div>

<div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<style>
    .modal-header .close {
        color: white !important;
        opacity: 1;
    }
    .modal-header .close:hover {
        opacity: 0.8;
    }
</style>