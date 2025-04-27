<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
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
                    <th>Slug</th>
                    <td>{{ $detailPengumuman->peg_slug }}</td>
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