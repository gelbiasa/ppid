@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;
    $kategoriFooterUrl = WebMenuModel::getDynamicMenuUrl('kategori-footer');
@endphp
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
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriFooterUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($kategoriFooterUrl . '/editData/' . $item->kategori_footer_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($kategoriFooterUrl . '/detailData/' . $item->kategori_footer_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriFooterUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($kategoriFooterUrl . '/deleteData/' . $item->kategori_footer_id) }}')">
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
     {{ $kategoriFooter->appends(['search' => $search])->links() }}
 </div>