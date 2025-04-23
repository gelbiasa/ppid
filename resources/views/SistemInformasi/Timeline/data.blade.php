@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;
    $timelineUrl = WebMenuModel::getDynamicMenuUrl('timeline');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $timeline->firstItem() }} to {{ $timeline->lastItem() }} of {{ $timeline->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kategori Timeline</th>
            <th width="45%">Judul Timeline</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($timeline as $key => $item)
        <tr>
            <td>{{ ($timeline->currentPage() - 1) * $timeline->perPage() + $key + 1 }}</td>
            <td>{{ $item->TimelineKategoriForm->kf_nama ?? '-' }}</td>
            <td>{{ $item->judul_timeline }}</td>
            <td>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $timelineUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($timelineUrl . '/editData/' . $item->timeline_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                    <button class="btn btn-sm btn-info"
                        onclick="modalAction('{{ url($timelineUrl . '/detailData/' . $item->timeline_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $timelineUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($timelineUrl . '/deleteData/' . $item->timeline_id) }}')">
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
    {{ $timeline->appends(['search' => $search])->links() }}
</div>