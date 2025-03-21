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
                    <th width="200">Kategori Pengumuman</th>
                    <td>{{ $pengumuman->PengumumanDinamis->pd_nama_submenu ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Judul Pengumuman</th>
                    <td>{{ $pengumuman->peg_judul ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Slug</th>
                    <td>{{ $pengumuman->peg_slug }}</td>
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
                @if($pengumuman->updated_by)
                <tr>
                    <th>Terakhir Diperbarui</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($pengumuman->updated_at)) }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $pengumuman->updated_by }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Detail Konten</h5>
        </div>
        <div class="card-body">
            @if($pengumuman->UploadPengumuman->up_type == 'link')
                <h6>URL Tujuan:</h6>
                <div class="mb-3">
                    <a href="{{ $pengumuman->UploadPengumuman->up_value }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt mr-1"></i> 
                        {{ $pengumuman->UploadPengumuman->up_value }}
                    </a>
                </div>
            @elseif($pengumuman->UploadPengumuman->up_type == 'file')
                @if($pengumuman->UploadPengumuman->up_thumbnail)
                <h6>Thumbnail:</h6>
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $pengumuman->UploadPengumuman->up_thumbnail) }}" class="img-thumbnail" style="max-height: 200px;">
                </div>
                @endif

                <h6>File:</h6>
                <div class="mb-3">
                    <a href="{{ asset('storage/' . $pengumuman->UploadPengumuman->up_value) }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-file-download mr-1"></i> Lihat File
                    </a>
                    <span class="ml-2 text-muted">{{ basename($pengumuman->UploadPengumuman->up_value) }}</span>
                </div>
            @elseif($pengumuman->UploadPengumuman->up_type == 'konten')
                @if($pengumuman->UploadPengumuman->up_thumbnail)
                <h6>Thumbnail:</h6>
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $pengumuman->UploadPengumuman->up_thumbnail) }}" class="img-thumbnail" style="max-height: 200px;">
                </div>
                @endif

                <h6>Konten:</h6>
                <div class="content-preview border p-3 rounded">
                    {!! $pengumuman->UploadPengumuman->up_konten !!}
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada detail konten yang tersedia.
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<style>
    .content-preview {
        max-height: 500px;
        overflow-y: auto;
        background-color: #fff;
    }
    .content-preview img {
        max-width: 100%;
        height: auto;
    }
</style>