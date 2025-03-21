<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $berita->firstItem() }} to {{ $berita->lastItem() }} of {{ $berita->total() }} results
     </div>
 </div>
  
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="20%">Kategori</th>
             <th width="25%">Judul/Link</th>
             <th width="15%">Tipe</th>
             <th width="15%">Status</th>
             <th width="20%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($berita as $key => $item)
         <tr>
             <td>{{ ($berita->currentPage() - 1) * $berita->perPage() + $key + 1 }}</td>
             <td>{{ $item->BeritaDinamis->bd_nama_submenu ?? '-' }}</td>
             <td>
                 @if($item->berita_type == 'file')
                     {{ $item->berita_judul }}
                 @else
                     <a href="{{ $item->berita_link }}" target="_blank">{{ $item->berita_link }}</a>
                 @endif
             </td>
             <td>
                 <span class="badge badge-{{ $item->berita_type == 'file' ? 'primary' : 'success' }}">
                     {{ ucfirst($item->berita_type) }}
                 </span>
             </td>
             <td>
                 <span class="badge badge-{{ $item->status_berita == 'aktif' ? 'success' : 'danger' }}">
                     {{ ucfirst($item->status_berita) }}
                 </span>
             </td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/upload-berita/editData/{$item->berita_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/upload-berita/detailData/{$item->berita_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/upload-berita/deleteData/{$item->berita_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="6" class="text-center">
                 @if(!empty($search))
                     Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                 @else
                     Tidak ada data
                 @endif
             </td>
         </tr>
         @endforelse
     </tbody>
 </table>
  
 <div class="mt-3">
     {{ $berita->appends(['search' => $search])->links() }}
 </div>