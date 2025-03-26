<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $mediaDinamis->firstItem() }} to {{ $mediaDinamis->lastItem() }} of {{ $mediaDinamis->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="10%">Nomor</th>
             <th width="60%">Kategori Media</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($mediaDinamis as $key => $item)
         <tr>
             <td>{{ ($mediaDinamis->currentPage() - 1) * $mediaDinamis->perPage() + $key + 1 }}</td>
             <td>{{ $item->md_kategori_media }}</td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/media-dinamis/editData/{$item->media_dinamis_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/media-dinamis/detailData/{$item->media_dinamis_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/media-dinamis/deleteData/{$item->media_dinamis_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="3" class="text-center">
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
     {{ $mediaDinamis->appends(['search' => $search])->links() }}
 </div>