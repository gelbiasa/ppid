<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $berita->firstItem() }} to {{ $berita->lastItem() }} of {{ $berita->total() }} results
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
        @forelse($berita as $key => $item)
        <tr>
            <td>{{ ($berita->currentPage() - 1) * $berita->perPage() + $key + 1 }}</td>
            <td>{{ $item->BeritaDinamis ? $item->BeritaDinamis->bd_nama_submenu : '-' }}</td>
            <td>{{ $item->berita_judul }}</td>
            <td>
                <span class="badge {{ $item->status_berita == 'aktif' ? 'badge-success' : 'badge-danger' }}">
                    {{ $item->status_berita }}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/berita/editData/{$item->berita_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/berita/detailData/{$item->berita_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/berita/deleteData/{$item->berita_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
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
    {{ $berita->appends(['search' => $search])->links() }}
</div>