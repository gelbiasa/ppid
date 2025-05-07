@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;
    $detailRegulasiUrl = WebMenuModel::getDynamicMenuUrl('detail-regulasi');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $regulasi->firstItem() }} to {{ $regulasi->lastItem() }} of {{ $regulasi->total() }} results
     </div>
 </div>
 
 <table class="table table-bordered table-striped table-hover table-sm">
     <thead>
         <tr>
             <th width="5%">Nomor</th>
             <th width="30%">Kategori Regulasi</th>
             <th width="30%">Judul</th>
             <th width="10%">Tipe</th>
             <th width="15%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($regulasi as $key => $item)
         <tr>
             <td>{{ ($regulasi->currentPage() - 1) * $regulasi->perPage() + $key + 1 }}</td>
             <td>{{ $item->KategoriRegulasi->kr_nama_kategori }}</td>
             <td>{{ $item->reg_judul }}</td>
             <td>
                 @if($item->reg_tipe_dokumen == 'file')
                     <span class="badge badge-primary">File</span>
                 @else
                     <span class="badge badge-info">Link</span>
                 @endif
             </td>
             <td>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailRegulasiUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($detailRegulasiUrl . '/editData/' . $item->regulasi_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($detailRegulasiUrl . '/detailData/' . $item->regulasi_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailRegulasiUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($detailRegulasiUrl . '/deleteData/' . $item->regulasi_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
            </td>
         </tr>
         @empty
         <tr>
             <td colspan="6" class="text-center">
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
     {{ $regulasi->appends(['search' => $search])->links() }}
 </div>
