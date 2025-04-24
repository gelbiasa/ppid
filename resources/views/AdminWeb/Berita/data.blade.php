@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;
    $detailBeritaUrl = WebMenuModel::getDynamicMenuUrl('detail-berita');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $detailBerita->firstItem() }} to {{ $detailBerita->lastItem() }} of {{ $detailBerita->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kategori</th>
            <th width="25%">Judul</th>
            <th width="15%">Status</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailBerita as $key => $item)
        <tr>
            <td>{{ ($detailBerita->currentPage() - 1) * $detailBerita->perPage() + $key + 1 }}</td>
            <td>{{ $item->BeritaDinamis ? $item->BeritaDinamis->bd_nama_submenu : '-' }}</td>
            <td>{{ $item->berita_judul }}</td>
            <td>
                <span class="badge {{ $item->status_berita == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                    {{ $item->status_berita }}
                </span>
            </td>
            <td>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $detailBeritaUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($detailBeritaUrl . '/editData/' . $item->berita_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($detailBeritaUrl . '/detailData/' . $item->berita_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $detailBeritaUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($detailBeritaUrl . '/deleteData/' . $item->berita_id) }}')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @endif
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
    {{ $detailBerita->appends(['search' => $search])->links() }}
</div>