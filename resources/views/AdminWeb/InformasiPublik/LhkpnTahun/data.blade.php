<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $lhkpn->firstItem() }} to {{ $lhkpn->lastItem() }} of {{ $lhkpn->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="15%">Tahun</th>
             <th width="50%">Judul Informasi</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($lhkpn as $key => $item)
         <tr>
             <td>{{ ($lhkpn->currentPage() - 1) * $lhkpn->perPage() + $key + 1 }}</td>
             <td>{{ $item->lhkpn_tahun }}</td>
             <td>{{ $item->lhkpn_judul_informasi }}</td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/lhkpn-tahun/editData/{$item->lhkpn_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/lhkpn-tahun/detailData/{$item->lhkpn_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/lhkpn-tahun/deleteData/{$item->lhkpn_id}") }}')">
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
     {{ $lhkpn->appends(['search' => $search])->links() }}
 </div>