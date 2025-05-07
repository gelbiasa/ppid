<div class="modal-header">
  <h5 class="modal-title">{{ $title }}</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <th width="200">Nama Pengguna</th>
              <td>{{ $user->nama_pengguna }}</td>
            </tr>
            <tr>
              <th>Email</th>
              <td>{{ $user->email_pengguna }}</td>
            </tr>
            <tr>
              <th>Nomor HP</th>
              <td>{{ $user->no_hp_pengguna }}</td>
            </tr>
            <tr>
              <th>NIK</th>
              <td>{{ $user->nik_pengguna }}</td>
            </tr>
            <tr>
              <th>Alamat</th>
              <td>{{ $user->alamat_pengguna }}</td>
            </tr>
            <tr>
              <th>Pekerjaan</th>
              <td>{{ $user->pekerjaan_pengguna }}</td>
            </tr>
            <tr>
              <th>Tanggal Dibuat</th>
              <td>{{ date('d-m-Y H:i:s', strtotime($user->created_at)) }}</td>
            </tr>
            <tr>
              <th>Dibuat Oleh</th>
              <td>{{ $user->created_by }}</td>
            </tr>
            @if($user->updated_by)
            <tr>
              <th>Terakhir Diperbarui</th>
              <td>{{ date('d-m-Y H:i:s', strtotime($user->updated_at)) }}</td>
            </tr>
            <tr>
              <th>Diperbarui Oleh</th>
              <td>{{ $user->updated_by }}</td>
            </tr>
            @endif
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      @if($user->upload_nik_pengguna)
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">KTP</h5>
          </div>
          <div class="card-body text-center">
            <img src="{{ asset('storage/' . $user->upload_nik_pengguna) }}" alt="KTP {{ $user->nama_pengguna }}" class="img-fluid">
            <div class="mt-2">
              <a href="{{ asset('storage/' . $user->upload_nik_pengguna) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Lihat Ukuran Penuh
              </a>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Hak Akses</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Level</th>
          </tr>
        </thead>
        <tbody>
          @forelse($user->hakAkses as $index => $hakAkses)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $hakAkses->hak_akses_kode }}</td>
              <td>{{ $hakAkses->hak_akses_nama }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">Tidak ada hak akses</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>