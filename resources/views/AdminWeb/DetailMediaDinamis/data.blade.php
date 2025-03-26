<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th width="5%">Nomor</th>
            <th width="15%">Kategori</th>
            <th width="20%">Judul Media</th>
            <th width="15%">Tipe Media</th>
            <th width="25%">Media</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($detailMediaDinamis as $key => $item)
        <tr>
            <td>{{ ($detailMediaDinamis->currentPage() - 1) * $detailMediaDinamis->perPage() + $key + 1 }}</td>
            <td>{{ $item->mediaDinamis->md_kategori_media ?? '-' }}</td>
            <td>{{ $item->dm_judul_media }}</td>
            <td>{{ ucfirst($item->dm_type_media) }}</td>
            <td>
                @if($item->dm_type_media == 'file')
                    {{ basename($item->dm_media_upload) }}
                @else
                    {{ $item->dm_media_upload }}
                @endif
            </td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/media-detail/editData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/media-detail/detailData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/media-detail/deleteData/{$item->detail_media_dinamis_id}") }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">
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