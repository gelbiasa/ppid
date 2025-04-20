@php
    use App\Models\Website\WebMenuModel;
    use App\Models\HakAkses\HakAksesModel;
    $ketentuanPelaporanUrl = WebMenuModel::getDynamicMenuUrl('management-level');
@endphp
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
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $ketentuanPelaporanUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/editData/' . $item->ketentuan_pelaporan_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                    <button class="btn btn-sm btn-info"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/detailData/' . $item->ketentuan_pelaporan_id) }}')">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                @if(
                    Auth::user()->level->level_kode === 'SAR' ||
                    HakAksesModel::cekHakAkses(Auth::user()->user_id, $ketentuanPelaporanUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($ketentuanPelaporanUrl . '/deleteData/' . $item->ketentuan_pelaporan_id) }}')">
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
    {{ $ketentuanPelaporan->appends(['search' => $search])->links() }}
</div>