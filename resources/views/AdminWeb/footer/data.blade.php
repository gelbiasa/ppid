<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $footers->firstItem() }} to {{ $footers->lastItem() }} of {{ $footers->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Kategori</th>
            <th width="25%">Judul</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($footers as $key => $footer)
        <tr>
            <td>{{ ($footers->currentPage() - 1) * $footers->perPage() + $key + 1 }}</td>
            <td>{{ $footer->kategoriFooter ? $footer->kategoriFooter->kt_footer_nama : '-' }}</td>
            <td>{{ $footer->f_judul_footer }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/footer/editData/{$footer->footer_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/footer/detailData/{$footer->footer_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/footer/deleteData/{$footer->footer_id}") }}')">
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
    {{ $footers->appends(['search' => $search])->links() }}
</div>