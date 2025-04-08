<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $detailLhkpn->firstItem() ?? 0 }} to {{ $detailLhkpn->lastItem() ?? 0 }} of {{ $detailLhkpn->total() ?? 0 }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">No</th>
             <th width="20%">Nama Karyawan</th>
             <th width="15%">Tahun</th>
             <th width="30%">Judul Informasi</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($detailLhkpn as $key => $item)
         <tr>
             <td>{{ ($detailLhkpn->currentPage() - 1) * $detailLhkpn->perPage() + $key + 1 }}</td>
             <td>{{ $item->dl_nama_karyawan }}</td>
             <td>{{ $item->lhkpn->lhkpn_tahun }}</td>
             <td>{{ $item->lhkpn->lhkpn_judul_informasi }}</td>
             <td>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/detailData/{$item->detail_lhkpn_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/editData/{$item->detail_lhkpn_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/deleteData/{$item->detail_lhkpn_id}") }}')">
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
{{ $detailLhkpn->appends(['search' => $search])->links() }}
 </div>