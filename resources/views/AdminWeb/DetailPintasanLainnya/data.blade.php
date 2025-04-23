<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $detailPintasanLainnya->firstItem() ?? 0 }} to {{ $detailPintasanLainnya->lastItem() ?? 0 }} of {{ $detailPintasanLainnya->total() ?? 0 }} results
     </div>
 </div>

 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th width="5%">No</th>
             <th width="20%">Kategori Pintasan</th>
             <th width="30%">Judul Pintasan</th>
             <th width="25%">URL</th>
             <th width="20%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($detailPintasanLainnya as $key => $item)
         <tr>
             <td table-data-label="No" class="text-center">{{ ($detailPintasanLainnya->currentPage() - 1) * $detailPintasanLainnya->perPage() + $key + 1 }}</td>
             <td table-data-label="Kategori Pintasan" class="text-center">{{ $item->pintasanLainnya->tpl_nama_kategori ?? 'N/A' }}</td>
             <td table-data-label="Judul Pintasan" class="text-center">{{ $item->dpl_judul }}</td>
             <td table-data-label="URL" class="text-truncate" style="max-width: 200px;">
                <a href="{{ $item->dpl_url }}" target="_blank">{{ $item->dpl_url }}</a>
             </td>
             <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/DetailPintasanLainnya/editData/{$item->detail_pintasan_lainnya_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/DetailPintasanLainnya/detailData/{$item->detail_pintasan_lainnya_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/DetailPintasanLainnya/deleteData/{$item->detail_pintasan_lainnya_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="5" class="text-center">
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
</div>

 <div class="mt-3">
     {{ $detailPintasanLainnya->appends(['search' => $search])->links() }}
 </div>