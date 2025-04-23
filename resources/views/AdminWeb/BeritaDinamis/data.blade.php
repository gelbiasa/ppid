@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;
    $kategoriBeritaUrl = WebMenuModel::getDynamicMenuUrl('kategori-berita');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{ $kategoriBerita->firstItem() }} to {{ $kategoriBerita->lastItem() }} of {{ $kategoriBerita->total() }}
        results
    </div>
</div>

<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="10%">Nomor</th>
            <th width="60%">Nama Submenu Berita</th>
            <th width="30%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kategoriBerita as $key => $item)
            <tr>
                <td>{{ ($kategoriBerita->currentPage() - 1) * $kategoriBerita->perPage() + $key + 1 }}</td>
                <td>{{ $item->bd_nama_submenu }}</td>
                <td>
                    @if(
                        Auth::user()->level->level_kode === 'SAR' ||
                        HakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriBeritaUrl, 'update')
                    )
                        <button class="btn btn-sm btn-warning"
                            onclick="modalAction('{{ url($kategoriBeritaUrl . '/editData/' . $item->berita_dinamis_id) }}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    @endif
                    <button class="btn btn-sm btn-info"
                        onclick="modalAction('{{ url($kategoriBeritaUrl . '/detailData/' . $item->berita_dinamis_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                    @if(
                        Auth::user()->level->level_kode === 'SAR' ||
                        HakAksesModel::cekHakAkses(Auth::user()->user_id, $kategoriBeritaUrl, 'delete')
                    )
                        <button class="btn btn-sm btn-danger"
                            onclick="modalAction('{{ url($kategoriBeritaUrl . '/deleteData/' . $item->berita_dinamis_id) }}')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    @endif
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
    {{ $kategoriBerita->appends(['search' => $search])->links() }}
</div>