<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="card">
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Kategori Form</th>
            <td>{{ $ketentuanPelaporan->PelaporanKategoriForm->kf_nama ?? 'Tidak ada' }}</td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($ketentuanPelaporan->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $ketentuanPelaporan->created_by }}</td>
          </tr>
          @if($ketentuanPelaporan->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($ketentuanPelaporan->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $ketentuanPelaporan->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
    
    <div class="card mt-3">
      <div class="card-header">
        <h5 class="card-title">{{ $ketentuanPelaporan->kp_judul }}</h5>
      </div>
      <div class="card-body">
        <div class="border-bottom mb-3"></div>
        <div class="ketentuan-content">
          {!! $ketentuanPelaporan->kp_konten !!}
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>
  
  <style>
    .ketentuan-content img {
      max-width: 100%;
      height: auto;
    }
    .ketentuan-content table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
    }
    .ketentuan-content table td,
    .ketentuan-content table th {
      border: 1px solid #dee2e6;
      padding: 0.5rem;
    }
  </style>