@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;
    $regulasiDinamisUrlUrl = WebMenuModel::getDynamicMenuUrl('regulasi-dinamis');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $RegulasiDinamis->firstItem() }} to {{ $RegulasiDinamis->lastItem() }} of {{ $RegulasiDinamis->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="65%">Nama Regulasi Dinamis</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($RegulasiDinamis as $key => $item)
         <tr>
             <td>{{ ($RegulasiDinamis->currentPage() - 1) * $RegulasiDinamis->perPage() + $key + 1 }}</td>
             <td>{{ $item->rd_judul_reg_dinamis }}</td>
             <td>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrlUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/editData/' . $item->regulasi_dinamis_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/detailData/' . $item->regulasi_dinamis_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $regulasiDinamisUrlUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($regulasiDinamisUrlUrl . '/deleteData/' . $item->regulasi_dinamis_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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
     {{ $RegulasiDinamis->appends(['search' => $search])->links() }}
 </div>