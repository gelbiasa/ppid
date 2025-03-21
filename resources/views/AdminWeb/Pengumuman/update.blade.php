<div class="modal-header">
    <h5 class="modal-title">Edit Pengumuman</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdatePengumuman" action="{{ url('AdminWeb/Pengumuman/updateData/' . $pengumuman->pengumuman_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pengumuman_id" value="{{ $pengumuman->pengumuman_id }}">

        <div class="form-group">
            <label for="kategori_pengumuman">Kategori Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_pengumuman" name="t_pengumuman[fk_m_pengumuman_dinamis]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriPengumuman as $kategori)
                    <option value="{{ $kategori->pengumuman_dinamis_id }}" {{ $pengumuman->fk_m_pengumuman_dinamis == $kategori->pengumuman_dinamis_id ? 'selected' : '' }}>
                        {{ $kategori->pd_nama_submenu }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_pengumuman_error"></div>
        </div>

        <div class="form-group">
            <label for="tipe_pengumuman">Tipe Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="tipe_pengumuman" name="up_type">
                <option value="">-- Pilih Tipe --</option>
                <option value="link" {{ $pengumuman->UploadPengumuman->up_type == 'link' ? 'selected' : '' }}>Link</option>
                <option value="file" {{ $pengumuman->UploadPengumuman->up_type == 'file' ? 'selected' : '' }}>File</option>
                <option value="konten" {{ $pengumuman->UploadPengumuman->up_type == 'konten' ? 'selected' : '' }}>Konten</option>
            </select>
            <div class="invalid-feedback" id="tipe_pengumuman_error"></div>
        </div>

        <!-- Judul Pengumuman (disembunyikan untuk tipe link) -->
        <div class="form-group" id="judul_container" style="{{ $pengumuman->UploadPengumuman->up_type == 'link' ? 'display: none;' : '' }}">
            <label for="judul_pengumuman">Judul Pengumuman <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_pengumuman" name="t_pengumuman[peg_judul]" maxlength="255" value="{{ $pengumuman->peg_judul }}">
            <div class="invalid-feedback" id="judul_pengumuman_error"></div>
        </div>

        <!-- Thumbnail (disembunyikan untuk tipe link) -->
        <div class="form-group" id="thumbnail_container" style="{{ $pengumuman->UploadPengumuman->up_type == 'link' ? 'display: none;' : '' }}">
            <label for="thumbnail">Thumbnail {{ $pengumuman->UploadPengumuman->up_thumbnail ? '' : '<span class="text-danger">*</span>' }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="thumbnail" name="up_thumbnail" accept="image/*">
                <label class="custom-file-label" for="thumbnail">{{ $pengumuman->UploadPengumuman->up_thumbnail ? 'Ganti thumbnail' : 'Pilih file' }}</label>
            </div>
            <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 10MB</small>
            <div class="invalid-feedback" id="thumbnail_error"></div>
            <div class="mt-2" id="thumbnail_preview" style="{{ $pengumuman->UploadPengumuman->up_thumbnail ? '' : 'display: none;' }}">
                @if($pengumuman->UploadPengumuman->up_thumbnail)
                    <img src="{{ asset('storage/' . $pengumuman->UploadPengumuman->up_thumbnail) }}" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @else
                    <img src="" id="thumbnail_image" class="img-thumbnail" style="max-height: 200px;">
                @endif
            </div>
        </div>

        <!-- URL (hanya untuk tipe link) -->
        <div class="form-group" id="url_container" style="{{ $pengumuman->UploadPengumuman->up_type == 'link' ? '' : 'display: none;' }}">
            <label for="url">URL <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="url" name="up_value" placeholder="https://..." value="{{ $pengumuman->UploadPengumuman->up_type == 'link' ? $pengumuman->UploadPengumuman->up_value : '' }}">
            <div class="invalid-feedback" id="url_error"></div>
        </div>

        <!-- File (hanya untuk tipe file) -->
        <div class="form-group" id="file_container" style="{{ $pengumuman->UploadPengumuman->up_type == 'file' ? '' : 'display: none;' }}">
            <label for="file">File {{ $pengumuman->UploadPengumuman->up_value ? '' : '<span class="text-danger">*</span>' }}</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="up_value">
                <label class="custom-file-label" for="file">{{ $pengumuman->UploadPengumuman->up_value ? 'Ganti file' : 'Pilih file' }}</label>
            </div>
            <small class="form-text text-muted">Ukuran maksimal: 50MB</small>
            <div class="invalid-feedback" id="file_error"></div>
            @if($pengumuman->UploadPengumuman->up_type == 'file' && $pengumuman->UploadPengumuman->up_value)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $pengumuman->UploadPengumuman->up_value) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-file mr-1"></i> Lihat File
                    </a>
                    <span class="ml-2 text-muted">{{ basename($pengumuman->UploadPengumuman->up_value) }}</span>
                </div>
            @endif
        </div>

        <!-- Konten (hanya untuk tipe konten) -->
        <div class="form-group" id="konten_container" style="{{ $pengumuman->UploadPengumuman->up_type == 'konten' ? '' : 'display: none;' }}">
            <label for="konten">Konten <span class="text-danger">*</span></label>
            <textarea id="konten" name="up_konten" class="form-control">{{ $pengumuman->UploadPengumuman->up_type == 'konten' ? $pengumuman->UploadPengumuman->up_konten : '' }}</textarea>
            <div class="invalid-feedback" id="konten_error"></div>
        </div>

        <div class="form-group">
            <label for="status_pengumuman">Status Pengumuman <span class="text-danger">*</span></label>
            <select class="form-control" id="status_pengumuman" name="t_pengumuman[status_pengumuman]">
                <option value="">-- Pilih Status --</option>
                <option value="aktif" {{ $pengumuman->status_pengumuman == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak aktif" {{ $pengumuman->status_pengumuman == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            <div class="invalid-feedback" id="status_pengumuman_error"></div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan Perubahan
    </button>
</div>

<!-- Include Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize custom file input
        bsCustomFileInput.init();
        
        // Initialize Summernote (with delay to ensure modal is fully loaded)
        setTimeout(function () {
            $('#konten').summernote({
                placeholder: 'Tuliskan konten pengumuman di sini...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        for (let i = 0; i < files.length; i++) {
                            uploadImage(files[i]);
                        }
                    }
                }
            });
        }, 100);

        // Handle tipe pengumuman change
        $('#tipe_pengumuman').on('change', function () {
            const type = $(this).val();
            
            // Reset form fields
            $('#judul_container, #thumbnail_container, #url_container, #file_container, #konten_container').hide();
            
            // Show relevant fields based on selected type
            switch (type) {
                case 'link':
                    $('#url_container').show();
                    break;
                case 'file':
                    $('#judul_container, #thumbnail_container, #file_container').show();
                    break;
                case 'konten':
                    $('#judul_container, #thumbnail_container, #konten_container').show();
                    break;
            }
        });
        
        // Handle thumbnail preview
        $('#thumbnail').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#thumbnail_image').attr('src', e.target.result);
                    $('#thumbnail_preview').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Function to upload image for Summernote
        function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ url("AdminWeb/Pengumuman/uploadImage") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        $('#konten').summernote('insertImage', response.url);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal mengunggah gambar'
                        });
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengunggah gambar. Silakan coba lagi.'
                    });
                }
            });
        }

        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function () {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Handle submit form
        $('#btnSubmitForm').on('click', function () {
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('.note-editor').removeClass('border border-danger');
            
            const form = $('#formUpdatePengumuman');
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
                        $('#myModal').modal('hide');
                        $('#table_pengumuman').DataTable().ajax.reload();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        if (response.errors) {
                            // Tampilkan pesan error pada masing-masing field
                            $.each(response.errors, function (key, value) {
                                if (key.startsWith('t_pengumuman.')) {
                                    const fieldName = key.replace('t_pengumuman.', '');
                                    if (fieldName === 'fk_m_pengumuman_dinamis') {
                                        $('#kategori_pengumuman').addClass('is-invalid');
                                        $('#kategori_pengumuman_error').html(value[0]);
                                    } else if (fieldName === 'peg_judul') {
                                        $('#judul_pengumuman').addClass('is-invalid');
                                        $('#judul_pengumuman_error').html(value[0]);
                                    } else if (fieldName === 'status_pengumuman') {
                                        $('#status_pengumuman').addClass('is-invalid');
                                        $('#status_pengumuman_error').html(value[0]);
                                    }
                                } else if (key === 'up_type') {
                                    $('#tipe_pengumuman').addClass('is-invalid');
                                    $('#tipe_pengumuman_error').html(value[0]);
                                } else if (key === 'up_value') {
                                    if ($('#tipe_pengumuman').val() === 'link') {
                                        $('#url').addClass('is-invalid');
                                        $('#url_error').html(value[0]);
                                    } else {
                                        $('#file').addClass('is-invalid');
                                        $('#file_error').html(value[0]);
                                    }
                                } else if (key === 'up_thumbnail') {
                                    $('#thumbnail').addClass('is-invalid');
                                    $('#thumbnail_error').html(value[0]);
                                } else if (key === 'up_konten') {
                                    $('.note-editor').addClass('border border-danger');
                                    $('#konten_error').html(value[0]).show();
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
    });
</script>