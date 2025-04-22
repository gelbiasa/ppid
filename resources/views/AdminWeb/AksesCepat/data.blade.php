<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="showing-text">
        Showing {{$aksesCepat->firstItem() }} to {{$aksesCepat->lastItem() }} of {{$aksesCepat->total() }} results
    </div>
</div>
<div class="table-responsive"></div>
<table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
    <thead class="text-center">
        <tr>
            <th width="5%">Nomor</th>
            <th width="20%">Judul Informasi Akses Cepat</th>
            <th width="20%">Icon Akses Cepat</th>
            <th width="20%">Icon Animasi Akses Cepat</th>
            <th width="15%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($aksesCepat as $key => $item)
        <tr>
            <td table-data-label>{{ ($aksesCepat->currentPage() - 1) *$aksesCepat->perPage() + $key + 1 }}</td>
           
            <td>{{ $item->ac_judul }}</td>
            <td>
                @if($item->ac_static_icon)
                    <img src="{{ asset('storage/akses_cepat_static_icons/' . basename($item->ac_static_icon)) }}"
                         alt="Static Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                 -
                @endif
            </td>
            <td>
                @if($item->ac_animation_icon)
                    <img src="{{ asset('storage/akses_cepat_animation_icons/' . basename($item->ac_animation_icon)) }}" 
                         alt="Animation Icon" class="img-thumbnail" style="max-height: 50px;">
                @else
                -
                @endif
            </td>
            <td>
                    <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/akses-cepat/editData/{$item->akses_cepat_id}") }}')">
                         <i class="fas fa-edit"></i> Edit
                     </button>
                     <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/akses-cepat/detailData/{$item->akses_cepat_id}") }}')">
                         <i class="fas fa-eye"></i> Detail
                     </button>
                     <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/akses-cepat/deleteData/{$item->akses_cepat_id}") }}')">
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
    {{$aksesCepat->appends(['search' => $search])->links() }}
</div>

@push('css')
<style>
    .img-thumbnail {
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }
</style>
@endpush