@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;
    $kategoriTahunLHKPNUrl = WebMenuModel::getDynamicMenuUrl('kategori-tahun-lhkpn');
@endphp
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
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriTahunLHKPNUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($kategoriTahunLHKPNUrl . '/editData/' . $item->lhkpn_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($kategoriTahunLHKPNUrl . '/detailData/' . $item->lhkpn_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriTahunLHKPNUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($kategoriTahunLHKPNUrl . '/deleteData/' . $item->lhkpn_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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