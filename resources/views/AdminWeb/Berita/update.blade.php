@php
  use App\Models\Website\WebMenuModel;
  $detailBeritaUrl = WebMenuModel::getDynamicMenuUrl('detail-berita');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Berita</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateBerita" action="{{ url($detailBeritaUrl . '/updateData/' . $detailBerita->berita_id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="berita_id" value="{{ $detailBerita->berita_id }}">

        <div class="form-group">
            <label for="kategori_berita">Kategori Berita <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_berita" name="t_berita[fk_m_berita_dinamis]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriBerita as $kategori)
                    <option value="{{ $kategori->berita_dinamis_id }}" {{ $detailBerita->fk_m_berita_dinamis == $kategori->berita_dinamis_id ? 'selected' : '' }}>
                        {{ $kategori->bd_nama_submenu }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_berita_error"></div>
        </div>

        <div class="form-group">
            <label for="judul_berita">Judul Berita <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_berita" name="t_berita[berita_judul]"
                maxlength="140" value="{{ $detailBerita->berita_judul }}">
            <div class="invalid-feedback" id="judul_berita_error"></div>
        </div>

        <div class="form-group">
            <label for="berita_thumbnail">Thumbnail Berita</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="berita_thumbnail" name="berita_thumbnail" accept="image/*">
                <label class="custom-file-label" for="berita_thumbnail">{{ $detailBerita->berita_thumbnail ? 'Ganti thumbnail' : 'Pilih file' }}</label>
            </div>
            <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 2.5MB</small>
            <div class="invalid-feedback" id="berita_thumbnail_error"></div>
            <div class="mt-2" id="thumbnail_preview" style="{{ $detailBerita->berita_thumbnail ? '' : 'display: none;' }}">
                @if($detailBerita->berita_thumbnail)
                    <img src="{{ asset('storage/' . $detailBerita->berita_thumbnail) }}" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @else
                    <img src="" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="berita_thumbnail_deskripsi">Deskripsi Thumbnail</label>
            <textarea class="form-control" id="berita_thumbnail_deskripsi" name="t_berita[berita_thumbnail_deskripsi]" maxlength="255" rows="2">{{ $detailBerita->berita_thumbnail_deskripsi }}</textarea>
            <small class="form-text text-muted">Maksimal 255 karakter</small>
            <div class="invalid-feedback" id="berita_thumbnail_deskripsi_error"></div>
        </div>

        <div class="form-group">
            <label for="berita_deskripsi">Konten Berita <span class="text-danger">*</span></label>
            <textarea id="berita_deskripsi" name="t_berita[berita_deskripsi]" class="form-control">{{ $detailBerita->berita_deskripsi }}</textarea>
            <div class="invalid-feedback" id="berita_deskripsi_error"></div>
        </div>

        <div class="form-group">
            <label for="status_berita">Status Berita <span class="text-danger">*</span></label>
            <select class="form-control" id="status_berita" name="t_berita[status_berita]">
                <option value="">-- Pilih Status --</option>
                <option value="aktif" {{ $detailBerita->status_berita == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $detailBerita->status_berita == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="status_berita_error"></div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<script>
    $(document).ready(function () {
        // Inisialisasi kembali event handler untuk tombol submit
        $('#btnSubmitForm').off('click').on('click', function () {
            console.log('Tombol submit diklik');
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('.note-editor').removeClass('border border-danger');

            const form = $('#formUpdateBerita');
            const formData = new FormData(form[0]);
            const button = $(this);

            // Tampilkan loading state pada tombol submit
            button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('.modal').modal('hide');

                        if (typeof reloadTable === 'function') {
                            reloadTable();
                        } else {
                            console.warn('Fungsi reloadTable tidak ditemukan, halaman mungkin perlu di-refresh manual');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        if (response.errors) {
                            // Tampilkan pesan error pada masing-masing field
                            $.each(response.errors, function (key, value) {
                                if (key.startsWith('t_berita.')) {
                                    const fieldName = key.replace('t_berita.', '');
                                    if (fieldName === 'fk_m_berita_dinamis') {
                                        $('#kategori_berita').addClass('is-invalid');
                                        $('#kategori_berita_error').html(value[0]);
                                    } else if (fieldName === 'berita_judul') {
                                        $('#judul_berita').addClass('is-invalid');
                                        $('#judul_berita_error').html(value[0]);
                                    } else if (fieldName === 'status_berita') {
                                        $('#status_berita').addClass('is-invalid');
                                        $('#status_berita_error').html(value[0]);
                                    } else if (fieldName === 'berita_thumbnail_deskripsi') {
                                        $('#berita_thumbnail_deskripsi').addClass('is-invalid');
                                        $('#berita_thumbnail_deskripsi_error').html(value[0]);
                                    } else if (fieldName === 'berita_deskripsi') {
                                        $('.note-editor').addClass('border border-danger');
                                        $('#berita_deskripsi_error').html(value[0]).show();
                                    }
                                } else if (key === 'berita_thumbnail') {
                                    $('#berita_thumbnail').addClass('is-invalid');
                                    $('#berita_thumbnail_error').html(value[0]);
                                } else {
                                    // Untuk field biasa
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}_error`).html(value[0]);
                                }
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Mohon periksa kembali input Anda'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    });
                },
                complete: function () {
                    // Kembalikan tombol submit ke keadaan semula
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });

        // Tangani pratinjau thumbnail
        $(document).on('change', '#berita_thumbnail', function(e) {
            console.log('Thumbnail berubah');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnail_image').attr('src', e.target.result);
                    $('#thumbnail_preview').show();
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>