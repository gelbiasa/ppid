<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriForm->firstItem() }} to {{ $kategoriForm->lastItem() }} of {{ $kategoriForm->total() }} results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="65%">Nama Kategori Form</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriForm as $key => $item)
        <tr>
            <td>{{ ($kategoriForm->currentPage() - 1) * $kategoriForm->perPage() + $key + 1 }}</td>
            <td>{{ $item->kf_nama }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/editData/{$item->kategori_form_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/detailData/{$item->kategori_form_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("SistemInformasi/KategoriForm/deleteData/{$item->kategori_form_id}") }}')">
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
    {{ $kategoriForm->appends(['search' => $search])->links() }}
</div>