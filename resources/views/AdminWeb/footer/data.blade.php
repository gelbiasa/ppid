<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $footer->firstItem() }} to {{ $footer->lastItem() }} of {{ $footer->total() }} results
    </div>
</div>
<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="35%">Judul Footer</th>
            <th width="35%">Kategori Footer</th>
            <th width="25%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($footer as $key => $item)
        <tr>
            <td>{{ ($footer->currentPage() - 1) * $footer->perPage() + $key + 1 }}</td>
            <td>{{ $item->f_judul_footer }}</td>
            <td>{{ $item->kategoriFooter->kt_footer_nama ?? 'Tidak Ada' }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/footer/editData/{$item->footer_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/footer/detailData/{$item->footer_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/footer/deleteData/{$item->footer_id}") }}')">
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
    {{ $footer->appends(['search' => $search])->links() }}
</div>