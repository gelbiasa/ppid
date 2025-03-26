<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $ketentuanPelaporan->firstItem() }} to {{ $ketentuanPelaporan->lastItem() }} of {{ $ketentuanPelaporan->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kategori Form</th>
            <th width="45%">Judul Ketentuan</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ketentuanPelaporan as $key => $item)
        <tr>
            <td>{{ ($ketentuanPelaporan->currentPage() - 1) * $ketentuanPelaporan->perPage() + $key + 1 }}</td>
            <td>{{ $item->PelaporanKategoriForm->kf_nama ?? '-' }}</td>
            <td>{{ $item->kp_judul }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("SistemInformasi/KetentuanPelaporan/editData/{$item->ketentuan_pelaporan_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("SistemInformasi/KetentuanPelaporan/detailData/{$item->ketentuan_pelaporan_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("SistemInformasi/KetentuanPelaporan/deleteData/{$item->ketentuan_pelaporan_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
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
    {{ $ketentuanPelaporan->appends(['search' => $search])->links() }}
</div>