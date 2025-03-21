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
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Kategori Pengumuman</th>
                    <td>{{ $pengumuman->PengumumanDinamis->pd_nama_submenu ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Judul Pengumuman</th>
                    <td>{{ $pengumuman->peg_judul ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tipe Konten</th>
                    <td>
                        @if($pengumuman->UploadPengumuman->up_type == 'link')
                            <span class="badge badge-info">Link</span>
                        @elseif($pengumuman->UploadPengumuman->up_type == 'file')
                            <span class="badge badge-primary">File</span>
                        @elseif($pengumuman->UploadPengumuman->up_type == 'konten')
                            <span class="badge badge-success">Konten</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($pengumuman->status_pengumuman == 'aktif')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Tidak Aktif</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($pengumuman->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $pengumuman->created_by }}</td>
                </tr>
            </table>
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
        onclick="confirmDelete('{{ url('AdminWeb/Pengumuman/deleteData/'.$pengumuman->pengumuman_id) }}')">
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
                    $('#table_pengumuman').DataTable().ajax.reload();
                    
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