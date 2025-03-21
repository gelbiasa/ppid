<div class="modal-header">
     <h5 class="modal-title">Detail Berita {{ ucfirst($berita->berita_type) }}</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Kategori Berita</th>
                     <td>{{ $berita->BeritaDinamis->bd_nama_submenu ?? 'Tidak ada' }}</td>
                 </tr>
                 <tr>
                     <th>Tipe Berita</th>
                     <td>
                         <span class="badge badge-{{ $berita->berita_type == 'file' ? 'primary' : 'success' }}">
                             {{ ucfirst($berita->berita_type) }}
                         </span>
                     </td>
                 </tr>
                 <tr>
                     <th>Status Berita</th>
                     <td>
                         <span class="badge badge-{{ $berita->status_berita == 'aktif' ? 'success' : 'danger' }}">
                             {{ ucfirst($berita->status_berita) }}
                         </span>
                     </td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($berita->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $berita->created_by }}</td>
                 </tr>
                 @if($berita->updated_by)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($berita->updated_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $berita->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>
 
     @if($berita->berita_type == 'file')
         @if($berita->berita_thumbnail)
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title">Thumbnail Berita</h5>
             </div>
             <div class="card-body text-center">
                 <img src="{{ asset('storage/' . $berita->berita_thumbnail) }}" 
                      alt="Thumbnail Berita" 
                      class="img-fluid" 
                      style="max-height: 400px;">
                 @if($berita->berita_thumbnail_deskripsi)
                     <p class="mt-2 text-muted">{{ $berita->berita_thumbnail_deskripsi }}</p>
                 @endif
             </div>
         </div>
         @endif
         
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title">{{ $berita->berita_judul }}</h5>
             </div>
             <div class="card-body">
                 <div class="border-bottom mb-3"></div>
                 <div class="berita-content">
                     {!! $berita->berita_deskripsi !!}
                 </div>
             </div>
         </div>
     @else
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title">Informasi Link</h5>
             </div>
             <div class="card-body">
                 <p>Link Berita: 
                     <a href="{{ $berita->berita_link }}" target="_blank" class="btn btn-primary btn-sm">
                         <i class="fas fa-external-link-alt mr-1"></i>Buka Link
                     </a>
                 </p>
             </div>
         </div>
     @endif
 
     @if($berita->uploadBerita->count() > 0)
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title">Upload Tambahan</h5>
             </div>
             <div class="card-body">
                 <table class="table table-bordered">
                     <thead>
                         <tr>
                             <th>Tipe</th>
                             <th>Nilai</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach($berita->uploadBerita as $upload)
                         <tr>
                             <td>
                                 <span class="badge badge-{{ $upload->ub_type == 'file' ? 'primary' : 'success' }}">
                                     {{ ucfirst($upload->ub_type) }}
                                 </span>
                             </td>
                             <td>
                                 @if($upload->ub_type == 'file')
                                     {{ $upload->ub_value }}
                                 @else
                                     <a href="{{ $upload->ub_value }}" target="_blank">{{ $upload->ub_value }}</a>
                                 @endif
                             </td>
                         </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
         </div>
     @endif
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>
 
 <style>
     .berita-content img {
         max-width: 100%;
         height: auto;
     }
     .berita-content table {
         width: 100%;
         border-collapse: collapse;
         margin-bottom: 1rem;
     }
     .berita-content table td,
     .berita-content table th {
         border: 1px solid #dee2e6;
         padding: 0.5rem;
     }
 </style>