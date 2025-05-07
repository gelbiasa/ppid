@php
  use App\Models\Website\WebMenuModel;
  use App\Models\HakAkses\SetHakAksesModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
  <div class="showing-text">
    @if(isset($users) && $users->total() > 0)
      Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
    @else
      0 results found
    @endif
  </div>
</div>

<div class="mb-3">
  @if(isset($currentLevel))
    <h4>{{ $currentLevel->hak_akses_nama }}</h4>
  @else
    <h4>Semua Pengguna</h4>
  @endif
</div>

<table class="table table-bordered table-striped table-hover table-sm">
  <thead>
    <tr>
      <th width="5%">No</th>
      <th width="20%">Nama</th>
      <th width="15%">Email</th>
      <th width="15%">No HP</th>
      <th width="15%">NIK</th>
      <th width="30%">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($users) && $users->count() > 0)
      @foreach($users as $key => $user)
        <tr>
          <td>{{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}</td>
          <td>{{ $user->nama_pengguna }}</td>
          <td>{{ $user->email_pengguna }}</td>
          <td>{{ $user->no_hp_pengguna }}</td>
          <td>{{ $user->nik_pengguna }}</td>
          <td>
            @if(
              Auth::user()->level->hak_akses_kode === 'SAR' ||
              (SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $managementUserUrl, 'update') && 
               (!$user->hakAkses->contains('hak_akses_kode', 'SAR') || Auth::user()->level->hak_akses_kode === 'SAR'))
            )
              <button class="btn btn-sm btn-warning"
                onclick="modalAction('{{ url($managementUserUrl . '/editData/' . $user->user_id) }}')">
                <i class="fas fa-edit"></i> Edit
              </button>
            @endif
            <button class="btn btn-sm btn-info"
              onclick="modalAction('{{ url($managementUserUrl . '/detailData/' . $user->user_id) }}')">
              <i class="fas fa-eye"></i> Detail
            </button>
            @if(
              Auth::user()->level->hak_akses_kode === 'SAR' ||
              (SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $managementUserUrl, 'delete') && 
               (!$user->hakAkses->contains('hak_akses_kode', 'SAR') || Auth::user()->level->hak_akses_kode === 'SAR'))
            )
              <button class="btn btn-sm btn-danger"
                onclick="modalAction('{{ url($managementUserUrl . '/deleteData/' . $user->user_id) }}')">
                <i class="fas fa-trash"></i> Hapus
              </button>
            @endif
          </td>
        </tr>
      @endforeach
    @else
      <tr>
        <td colspan="6" class="text-center">
          @if(isset($currentLevel))
            Level {{ $currentLevel->hak_akses_nama }} belum memiliki user
          @else
            @if(!empty($search))
              Tidak ada data yang cocok dengan pencarian "{{ $search }}"
            @else
              Tidak ada data
            @endif
          @endif
        </td>
      </tr>
    @endif
  </tbody>
</table>

@if(isset($users))
  <div class="mt-3">
    {{ $users->appends(['search' => $search, 'level_id' => $levelId ?? null])->links() }}
  </div>
@endif