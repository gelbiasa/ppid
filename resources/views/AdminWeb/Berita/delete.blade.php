@php
  use App\Models\Website\WebMenuModel;
  $detailBeritaUrl = WebMenuModel::getDynamicMenuUrl('detail-berita');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Konfirmasi Hapus Berita</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="alert alert-danger mt-3">
    <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus berita dengan detail berikut:
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <table class="table table-borderless">
        <tr>
          <th width="200">Kategori Berita</th>
          <td>{{ $detailBerita->kategoriBerita->bd_nama_submenu ?? 'Tidak ada' }}</td>
        </tr>
        <tr>
          <th>Judul Berita</th>
          <td>{{ $detailBerita->berita_judul }}</td>
        </tr>
        <tr>
          <th>Status Berita</th>
          <td>
            <span
              class="badge badge-pill {{ $detailBerita->status_berita == 'aktif' ? 'badge-success' : 'badge-danger' }} px-3 py-2">
              {{ ucfirst($detailBerita->status_berita) }}
            </span>
          </td>
        </tr>
        <tr>
          <th>Thumbnail</th>
          <td>
            @if($detailBerita->berita_thumbnail)
        <img src="{{ asset('storage/' . $detailBerita->berita_thumbnail) }}" alt="Thumbnail Berita"
          class="img-thumbnail" style="height: 80px;">
        @if($detailBerita->berita_thumbnail_deskripsi)
      <small class="d-block text-muted mt-2">
        {{ $detailBerita->berita_thumbnail_deskripsi }}
      </small>
    @endif
      @else
    -
  @endif
          </td>
        </tr>
        <tr>
          <th>Tanggal Dibuat</th>
          <td>{{ $detailBerita->created_at->format('d-m-Y H:i:s') }}</td>
        </tr>
        <tr>
          <th>Dibuat Oleh</th>
          <td>{{ $detailBerita->created_by }}</td>
        </tr>
        @if($detailBerita->updated_by)
      <tr>
        <th>Terakhir Diperbarui</th>
        <td>{{ $detailBerita->updated_at->format('d-m-Y H:i:s') }}</td>
      </tr>
      <tr>
        <th>Diperbarui Oleh</th>
        <td>{{ $detailBerita->updated_by }}</td>
      </tr>
    @endif
      </table>
    </div>
  </div>

  <!-- Content Card untuk Deskripsi Berita -->
  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <h6 class="card-title mb-0 font-weight-bold">Konten Berita</h6>
    </div>
    <div class="card-body">
      <div class="berita-content" style="max-height: 200px; overflow-y: auto;">
        {!! $detailBerita->berita_deskripsi !!}
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-danger" id="confirmDeleteButton"
    onclick="confirmDelete('{{ url($detailBeritaUrl . '/deleteData/' . $detailBerita->berita_id) }}')">
    <i class="fas fa-trash mr-1"></i> Hapus
  </button>
</div>

<style>
  /* Card Styling */
  .card {
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .card-header {
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background-color: #f8f9fa;
  }

  /* Content Styling */
  .berita-content {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }

  .berita-content img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    margin: 1rem 0;
  }

  .berita-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
    border-radius: 6px;
    overflow: hidden;
  }

  .berita-content table td,
  .berita-content table th {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
  }

  .berita-content table thead th {
    background-color: #f8f9fa;
  }

  /* Scrollbar Styling */
  .berita-content::-webkit-scrollbar {
    width: 6px;
  }

  .berita-content::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .berita-content::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 6px;
  }

  .berita-content::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
  }

  /* Badge styling */
  .badge-pill {
    font-weight: 500;
    letter-spacing: 0.5px;
  }
</style>

<script>
  function showDeleteConfirmation(url) {
    Swal.fire({
      title: 'Konfirmasi Penghapusan',
      text: "Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        confirmDelete(url);
      }
    });
  }

  // Perbaikan untuk fungsi confirmDelete di view delete.blade.php
  function confirmDelete(url) {
    const button = $('#confirmDeleteButton');

    button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);

    $.ajax({
      url: url,
      type: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        $('#myModal').modal('hide');

        if (response.success) {
          // Show success message before reloading the table
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: response.message || 'Berita berhasil dihapus'
          });

          // Kemudian reload tabel
          reloadTable();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message || 'Gagal menghapus berita'
          });

          button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
        }
      },
      error: function (xhr) {
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