<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $kategoriAkses->firstItem() }} to {{ $kategoriAkses->lastItem() }} of {{ $kategoriAkses->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="10%">Nomor</th>
             <th width="60%">Judul Kategori</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($kategoriAkses as $key => $item)
         <tr>
             <td>{{ ($kategoriAkses->currentPage() - 1) * $kategoriAkses->perPage() + $key + 1 }}</td>
             <td>{{ $item->mka_judul_kategori }}</td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/kategori-akses/editData/{$item->kategori_akses_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/kategori-akses/detailData/{$item->kategori_akses_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/kategori-akses/deleteData/{$item->kategori_akses_id}") }}')">
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
     {{ $kategoriAkses->appends(['search' => $search])->links() }}
 </div>