<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $kategoriFooter->firstItem() }} to {{ $kategoriFooter->lastItem() }} of {{ $kategoriFooter->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="30%">Kode Kategori Footer</th>
             <th width="35%">Nama Kategori Footer</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($kategoriFooter as $key => $item)
         <tr>
             <td>{{ ($kategoriFooter->currentPage() - 1) * $kategoriFooter->perPage() + $key + 1 }}</td>
             <td>{{ $item->kt_footer_kode }}</td>
             <td>{{ $item->kt_footer_nama }}</td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/kategori-footer/editData/{$item->kategori_footer_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/kategori-footer/detailData/{$item->kategori_footer_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/kategori-footer/deleteData/{$item->kategori_footer_id}") }}')">
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
     {{ $kategoriFooter->appends(['search' => $search])->links() }}
 </div>