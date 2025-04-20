<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $pintasanLainnya->firstItem() }} to {{ $pintasanLainnya->lastItem() }} of {{ $pintasanLainnya->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="30%">Nama Kategori Akses</th>
             <th width="30%">Nama Pintasan Lainnya</th>
             <th width="35%">Aksi</th>
         </tr>
     </thead>
     <tbody>
          @forelse($pintasanLainnya as $key => $item)
          <tr>
              <td>{{ ($pintasanLainnya->currentPage() - 1) * $pintasanLainnya->perPage() + $key + 1 }}</td>
              <td>{{ $item->kategoriAkses->mka_judul_kategori ?? 'Kategori Tidak Tersedia' }}</td>
              <td>{{ $item->tpl_nama_kategori }}</td>
              <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/pintasan-lainnya/editData/{$item->pintasan_lainnya_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/pintasan-lainnya/detailData/{$item->pintasan_lainnya_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/pintasan-lainnya/deleteData/{$item->pintasan_lainnya_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="4" class="text-center">
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
     {{ $pintasanLainnya->appends(['search' => $search])->links() }}
 </div>