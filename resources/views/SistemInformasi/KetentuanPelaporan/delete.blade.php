@php
  use App\Models\Website\WebMenuModel;
  $ketentuanPelaporanUrl = WebMenuModel::getDynamicMenuUrl('ketentuan-pelaporan');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Konfirmasi Hapus Ketentuan Pelaporan</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">    
    <div class="alert alert-danger mt-3">
      <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus ketentuan pelaporan dengan detail berikut:
    </div>
    
    <div class="card">
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Kategori Form</th>
            <td>{{ $ketentuanPelaporan->PelaporanKategoriForm->kf_nama ?? 'Tidak ada' }}</td>
          </tr>
          <tr>
            <th>Judul Ketentuan</th>
            <td>{{ $ketentuanPelaporan->kp_judul }}</td>
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
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
      onclick="confirmDelete('{{ url($ketentuanPelaporanUrl . '/deleteData/'.$ketentuanPelaporan->ketentuan_pelaporan_id) }}')">
      <i class="fas fa-trash mr-1"></i> Hapus
    </button>
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
  
  <script>
    function confirmDelete(url) {
      const button = $('#confirmDeleteButton');
      
      button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
      
      $.ajax({
        url: url,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#myModal').modal('hide');
          
          if (response.success) {
            reloadTable();
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message
            });
          }
        },
        error: function(xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
          });
          
          button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
        }
      });
    }
  </script>