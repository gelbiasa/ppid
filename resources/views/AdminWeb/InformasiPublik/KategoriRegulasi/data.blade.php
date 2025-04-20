<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $kategoriRegulasi->firstItem() }} to {{ $kategoriRegulasi->lastItem() }} of {{ $kategoriRegulasi->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="30%">Regulasi Dinamis</th>
             <th width="20%">Kode Kategori</th>
             <th width="30%">Nama Kategori Regulasi</th>
             <th width="15%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($kategoriRegulasi as $key => $item)
         <tr>
             <td>{{ ($kategoriRegulasi->currentPage() - 1) * $kategoriRegulasi->perPage() + $key + 1 }}</td>
             <td>{{ $item->RegulasiDinamis->rd_judul_reg_dinamis ?? 'Tidak ada' }}</td>
             <td>{{ $item->kr_kategori_reg_kode }}</td>
             <td>{{ $item->kr_nama_kategori }}</td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/editData/{$item->kategori_reg_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/detailData/{$item->kategori_reg_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/deleteData/{$item->kategori_reg_id}") }}')">
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
 
 <div class="mt-3">
     {{ $kategoriRegulasi->appends(['search' => $search])->links() }}
 </div>