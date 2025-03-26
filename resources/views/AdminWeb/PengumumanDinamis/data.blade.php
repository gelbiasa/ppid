<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $pengumumanDinamis->firstItem() }} to {{ $pengumumanDinamis->lastItem() }} of {{ $pengumumanDinamis->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="65%">Nama Submenu Pengumuman</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pengumumanDinamis as $key => $item)
        <tr>
            <td>{{ ($pengumumanDinamis->currentPage() - 1) * $pengumumanDinamis->perPage() + $key + 1 }}</td>
            <td>{{ $item->pd_nama_submenu }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/editData/{$item->pengumuman_dinamis_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/detailData/{$item->pengumuman_dinamis_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("AdminWeb/PengumumanDinamis/deleteData/{$item->pengumuman_dinamis_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
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
    {{ $pengumumanDinamis->appends(['search' => $search])->links() }}
</div>