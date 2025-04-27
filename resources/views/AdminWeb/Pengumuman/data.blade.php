@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;
    $detailPengumumanUrl = WebMenuModel::getDynamicMenuUrl('detail-pengumuman');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $detailPengumuman->firstItem() }} to {{ $detailPengumuman->lastItem() }} of {{ $detailPengumuman->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Kategori Pengumuman</th>
            <th width="25%">Judul</th>
            <th width="10%">Tipe</th>
            <th width="10%">Status</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailPengumuman as $key => $item)
        <tr>
            <td>{{ ($detailPengumuman->currentPage() - 1) * $detailPengumuman->perPage() + $key + 1 }}</td>
            <td>{{ $item->PengumumanDinamis->pd_nama_submenu ?? '-' }}</td>
            <td>{{ $item->peg_judul ?? '-' }}</td>
            <td>
                @if($item->UploadPengumuman)
                    @if($item->UploadPengumuman->up_type === 'link')
                        <span class="badge badge-info">Link</span>
                    @elseif($item->UploadPengumuman->up_type === 'file')
                        <span class="badge badge-primary">File</span>
                    @elseif($item->UploadPengumuman->up_type === 'konten')
                        <span class="badge badge-success">Konten</span>
                    @else
                        {{ $item->UploadPengumuman->up_type }}
                    @endif
                @else
                    -
                @endif
            </td>
            <td>
                @if($item->status_pengumuman === 'aktif')
                    <span class="status-badge status-aktif">Aktif</span>
                @else
                    <span class="status-badge status-tidak-aktif">Tidak Aktif</span>
                @endif
            </td>
            <td>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailPengumumanUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($detailPengumumanUrl . '/editData/' . $item->pengumuman_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($detailPengumumanUrl . '/detailData/' . $item->pengumuman_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $detailPengumumanUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($detailPengumumanUrl . '/deleteData/' . $item->pengumuman_id) }}')">
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
    {{ $detailPengumuman->appends(['search' => $search])->links() }}
</div>