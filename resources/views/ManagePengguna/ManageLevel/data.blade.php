@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\SetHakAksesModel;
    $managementLevelUrl = WebMenuModel::getDynamicMenuUrl('management-level');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $level->firstItem() }} to {{ $level->lastItem() }} of {{ $level->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kode Level</th>
            <th width="45%">Nama Level</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($level as $key => $item)
                <tr>
                    <td>{{ ($level->currentPage() - 1) * $level->perPage() + $key + 1 }}</td>
                    <td>{{ $item->hak_akses_kode }}</td>
                    <td>{{ $item->hak_akses_nama }}</td>
                    <td>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $managementLevelUrl, 'update')
                        )
                            <button class="btn btn-sm btn-warning"
                                onclick="modalAction('{{ url($managementLevelUrl . '/editData/' . $item->hak_akses_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        @endif
                            <button class="btn btn-sm btn-info"
                                onclick="modalAction('{{ url($managementLevelUrl . '/detailData/' . $item->hak_akses_id) }}')">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        @if(
                            Auth::user()->level->hak_akses_kode === 'SAR' ||
                            SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $managementLevelUrl, 'delete')
                        )
                            <button class="btn btn-sm btn-danger"
                                onclick="modalAction('{{ url($managementLevelUrl . '/deleteData/' . $item->hak_akses_id) }}')">
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
    {{ $level->appends(['search' => $search])->links() }}
</div>