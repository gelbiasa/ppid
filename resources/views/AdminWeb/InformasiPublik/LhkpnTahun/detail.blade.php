<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Informasi LHKPN</h5>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Tahun LHKPN</th>
                    <td>{{ $lhkpn->lhkpn_tahun ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Judul Informasi</th>
                    <td>{{ $lhkpn->lhkpn_judul_informasi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ date('d-m-Y H:i:s', strtotime($lhkpn->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $lhkpn->created_by ?? '-' }}</td>
                </tr>
                @if($lhkpn->updated_by ?? null)
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>{{ date('d-m-Y H:i:s', strtotime($lhkpn->updated_at)) }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui Oleh</th>
                        <td>{{ $lhkpn->updated_by }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Detail Deskripsi Informasi</h5>
        </div>
        <div class="card-body">
            {!! $lhkpn->lhkpn_deskripsi_informasi ?? '<div class="alert alert-info">Tidak ada deskripsi yang tersedia.</div>' !!}
        </div>
    </div>

    <!-- Jika terdapat file attachment atau lampiran, tambahkan bagian ini -->
    @if(isset($lhkpn->file_attachment) && $lhkpn->file_attachment)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Lampiran</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ asset('storage/' . $lhkpn->file_attachment) }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-file-download mr-1"></i> Lihat File
                    </a>
                    <span class="ml-2 text-muted">{{ basename($lhkpn->file_attachment) }}</span>
                </div>
            </div>
        </div>
    @endif
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

    .table th {
        font-weight: 600;
        color: #555;
    }
</style>