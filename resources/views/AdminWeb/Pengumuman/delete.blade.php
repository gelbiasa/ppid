@php
  use App\Models\Website\WebMenuModel;
  $detailPengumumanUrl = WebMenuModel::getDynamicMenuUrl('detail-pengumuman');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Konfirmasi Hapus Pengumuman</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">    
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus pengumuman dengan detail berikut:
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Informasi Pengumumn</h5>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Kategori Pengumuman</th>
                    <td>{{ $detailPengumuman->PengumumanDinamis->pd_nama_submenu ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Judul Pengumuman</th>
                    <td>{{ $detailPengumuman->peg_judul ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tipe Konten</th>
                    <td>
                        @if($detailPengumuman->UploadPengumuman->up_type == 'link')
                            <span class="badge badge-info">Link</span>
                        @elseif($detailPengumuman->UploadPengumuman->up_type == 'file')
                            <span class="badge badge-primary">File</span>
                        @elseif($detailPengumuman->UploadPengumuman->up_type == 'konten')
                            <span class="badge badge-success">Konten</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($detailPengumuman->status_pengumuman == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($detailPengumuman->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $detailPengumuman->created_by }}</td>
                </tr>
                @if($detailPengumuman->updated_by)
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>{{ date('d-m-Y H:i:s', strtotime($detailPengumuman->updated_at)) }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui Oleh</th>
                        <td>{{ $detailPengumuman->updated_by }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    @if($detailPengumuman->UploadPengumuman->up_thumbnail)

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Thumbnail</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $detailPengumuman->UploadPengumuman->up_thumbnail) }}"
                    class="img-fluid max-height-300" alt="Thumbnail Pengumuman">
            </div>
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Detail Konten</h5>
        </div>
        <div class="card-body">
            @if($detailPengumuman->UploadPengumuman->up_type == 'link')
                <h6>URL Tujuan:</h6>
                <div class="mb-3">
                    <a href="{{ $detailPengumuman->UploadPengumuman->up_value }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        {{ $detailPengumuman->UploadPengumuman->up_value }}
                    </a>
                </div>
            @elseif($detailPengumuman->UploadPengumuman->up_type == 'file')
                <div class="mb-3">
                    <a href="{{ asset('storage/' . $detailPengumuman->UploadPengumuman->up_value) }}" target="_blank"
                        class="btn btn-info">
                        <i class="fas fa-file-download mr-1"></i> Lihat File
                    </a>
                    <span class="ml-2 text-muted">{{ basename($detailPengumuman->UploadPengumuman->up_value) }}</span>
                </div>
            @elseif($detailPengumuman->UploadPengumuman->up_type == 'konten')

                {!! $detailPengumuman->UploadPengumuman->up_konten !!}

            @else
                <div class="alert alert-info">
                    Tidak ada detail konten yang tersedia.
                </div>
            @endif
        </div>
    </div>

    <div class="alert alert-warning mt-3">
        <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
        Menghapus pengumuman ini akan menghapus semua data terkait termasuk file dan thumbnail (jika ada). 
        Tindakan ini tidak dapat dibatalkan.
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
    onclick="confirmDelete('{{ url( $detailPengumumanUrl . '/deleteData/' . $detailPengumuman->pengumuman_id) }}')">
        <i class="fas fa-trash mr-1"></i> Hapus
    </button>
</div>

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