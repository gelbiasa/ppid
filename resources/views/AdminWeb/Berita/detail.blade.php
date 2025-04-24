<div class="modal-header bg-primary text-white py-3">
    <h5 class="modal-title font-weight-bold">Detail Berita</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body py-4">
    <!-- Metadata Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 font-weight-bold">Informasi Berita</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Kategori Berita</label>
                        <div class="font-weight-medium">{{ $detailBerita->kategoriBerita->bd_nama_submenu ?? 'Tidak ada' }}</div>
                    </div>
                    
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Status Berita</label>
                        <span class="badge badge-pill {{ $detailBerita->status_berita == 'aktif' ? 'badge-success' : 'badge-danger' }} px-3 py-2">
                            {{ $detailBerita->status_berita }}
                        </span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Tanggal Dibuat</label>
                        <div class="font-weight-medium">{{ $detailBerita->created_at->format('d-m-Y H:i:s') }}</div>
                    </div>
                    
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Dibuat Oleh</label>
                        <div class="font-weight-medium">{{ $detailBerita->created_by }}</div>
                    </div>
                </div>
            </div>
            
            @if($detailBerita->updated_by)
            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Terakhir Diperbarui</label>
                        <div class="font-weight-medium">{{ $detailBerita->updated_at->format('d-m-Y H:i:s') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-group mb-3">
                        <label class="d-block text-muted small">Diperbarui Oleh</label>
                        <div class="font-weight-medium">{{ $detailBerita->updated_by }}</div>
                    </div>
                </div>
            </div>
            @endif
            
            @if($detailBerita->berita_thumbnail)
            <div class="mt-3">
                <label class="d-block text-muted small">Thumbnail</label>
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $detailBerita->berita_thumbnail) }}" 
                         class="img-thumbnail rounded shadow-sm" 
                         style="max-width: 250px; height: auto; object-fit: cover;"
                         alt="Thumbnail Berita">
                    @if($detailBerita->berita_thumbnail_deskripsi)
                        <small class="d-block text-muted mt-2">
                            {{ $detailBerita->berita_thumbnail_deskripsi }}
                        </small>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Content Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 font-weight-bold">{{ $detailBerita->berita_judul }}</h5>
        </div>
        <div class="card-body">
            <div class="berita-content">
                {!! $detailBerita->berita_deskripsi !!}
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<style>
    /* Modal Styling */
    .modal-content {
        border-radius: 8px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .modal-header {
        border-radius: 8px 8px 0 0;
    }
    
    .modal-title {
        letter-spacing: 0.5px;
    }
    
    .modal-body {
        max-height: 75vh;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    
    .modal-footer {
        border-radius: 0 0 8px 8px;
    }
    
    /* Card Styling */
    .card {
        border: none;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
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
    
    /* Responsiveness */
    @media (max-width: 768px) {
        .modal-body {
            padding: 1rem;
            max-height: 80vh;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    /* Scrollbar Styling */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 6px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    /* Badge styling */
    .badge-pill {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    /* Info group labels */
    .info-group label {
        margin-bottom: 4px;
        font-weight: 600;
        color: #6c757d;
    }
    
    .font-weight-medium {
        font-weight: 500;
    }
</style>