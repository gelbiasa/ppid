<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Edit Berita</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="formUpdateBerita" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="berita_id" value="{{ $berita->berita_id }}">
    
    <div class="modal-body">
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-group">
                    <label for="fk_m_berita_dinamis">Kategori Berita <span class="text-danger">*</span></label>
                    <select class="form-control" id="fk_m_berita_dinamis" name="t_berita[fk_m_berita_dinamis]" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($beritaDinamis as $kategori)
                            <option value="{{ $kategori->berita_dinamis_id }}" {{ $berita->fk_m_berita_dinamis == $kategori->berita_dinamis_id ? 'selected' : '' }}>
                                {{ $kategori->bd_nama_submenu }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="fk_m_berita_dinamis_error"></div>
                </div>

                <div class="form-group">
                    <label for="berita_judul">Judul Berita <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="berita_judul" name="t_berita[berita_judul]" maxlength="140" required value="{{ $berita->berita_judul }}">
                    <div class="invalid-feedback" id="berita_judul_error"></div>
                </div>

                <div class="form-group">
                    <label>Status Berita <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_aktif" name="t_berita[status_berita]" class="custom-control-input" value="aktif" {{ $berita->status_berita == 'aktif' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status_aktif">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_nonaktif" name="t_berita[status_berita]" class="custom-control-input" value="nonaktif" {{ $berita->status_berita == 'nonaktif' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status_nonaktif">Nonaktif</label>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="status_berita_error"></div>
                </div>

                <div class="form-group">
                    <label for="berita_thumbnail">Thumbnail Berita</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="berita_thumbnail" name="berita_thumbnail" accept="image/*">
                        <label class="custom-file-label" for="berita_thumbnail">{{ $berita->berita_thumbnail ? 'Ganti thumbnail...' : 'Pilih file' }}</label>
                    </div>
                    <small class="form-text text-muted">Format yang didukung: JPG, PNG, GIF. Ukuran maks 2.5 MB</small>
                    <div class="invalid-feedback" id="berita_thumbnail_error"></div>
                </div>

                <div class="form-group" id="thumbnail-preview-container">
                    @if($berita->berita_thumbnail)
                    <div id="current-thumbnail" class="mt-2">
                        <label class="d-block text-muted small">Thumbnail Saat Ini:</label>
                        <img src="{{ asset('storage/' . $berita->berita_thumbnail) }}" alt="Current Thumbnail" class="img-thumbnail" style="height: 100px;">
                    </div>
                    @endif
                    
                    <div id="thumbnail-preview" class="mt-2 d-none">
                        <label class="d-block text-muted small">Thumbnail Baru:</label>
                        <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="berita_thumbnail_deskripsi">Deskripsi Thumbnail</label>
                    <input type="text" class="form-control" id="berita_thumbnail_deskripsi" name="t_berita[berita_thumbnail_deskripsi]" maxlength="255" value="{{ $berita->berita_thumbnail_deskripsi }}">
                    <div class="invalid-feedback" id="berita_thumbnail_deskripsi_error"></div>
                </div>

                <div class="form-group">
                    <label for="berita_deskripsi">Konten Berita <span class="text-danger">*</span></label>
                    <textarea id="berita_deskripsi" name="t_berita[berita_deskripsi]" class="form-control summernote">{!! $berita->berita_deskripsi !!}</textarea>
                    <div class="invalid-feedback" id="berita_deskripsi_error"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnUpdateForm">Perbarui</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Custom file input label
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        
        // Preview thumbnail if file is selected
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnail-preview').removeClass('d-none');
                $('#thumbnail-preview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Initialize Summernote
    $('#berita_deskripsi').summernote({
        placeholder: 'Tuliskan konten berita di sini...',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear', 'fontsize', 'fontname']], 
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'height', 'align']], 
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    uploadImage(files[i]);
                }
            }
        }
    });

    // Image upload function
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);

        $.ajax({
            url: '{{ url("adminweb/berita/uploadImage") }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#berita_deskripsi').summernote('insertImage', response.url);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal mengunggah gambar'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengunggah gambar'
                });
            }
        });
    }
    
    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Reset error messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        // Validate required fields
        if (!$('#fk_m_berita_dinamis').val()) {
            $('#fk_m_berita_dinamis').addClass('is-invalid');
            $('#fk_m_berita_dinamis_error').html('Kategori berita wajib dipilih');
            isValid = false;
        }
        
        if (!$('#berita_judul').val().trim()) {
            $('#berita_judul').addClass('is-invalid');
            $('#berita_judul_error').html('Judul berita wajib diisi');
            isValid = false;
        }
        
        if (!$('input[name="t_berita[status_berita]"]:checked').val()) {
            $('#status_berita_error').html('Status berita wajib dipilih');
            isValid = false;
        }
        
        const content = $('#berita_deskripsi').summernote('code');
        if (!content || content === '<p><br></p>') {
            $('#berita_deskripsi').next('.note-editor').addClass('is-invalid');
            $('#berita_deskripsi_error').html('Konten berita wajib diisi');
            isValid = false;
        }
        
        return isValid;
    }

    // Submit form
    $('#btnUpdateForm').click(function() {
        if (!validateForm()) {
            return false;
        }
        
        const button = $(this);
        const form = $('#formUpdateBerita');
        const formData = new FormData(form[0]);
        
        // Update summernote content
        formData.set('t_berita[berita_deskripsi]', $('#berita_deskripsi').summernote('code'));
        
        // Disable button
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...');
        
        $.ajax({
            url: '{{ url("adminweb/berita/updateData/".$berita->berita_id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    $('#myModal').modal('hide');
                    loadBeritaData(1, '');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                } else {
                    // Handle validation errors
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            const fieldName = key.replace('t_berita.', '');
                            
                            if (fieldName === 'berita_deskripsi') {
                                $('#berita_deskripsi').next('.note-editor').addClass('is-invalid');
                                $('#berita_deskripsi_error').html(value[0]);
                            } else {
                                $(`#${fieldName}`).addClass('is-invalid');
                                $(`#${fieldName}_error`).html(value[0]);
                            }
                        });
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal memperbarui data berita'
                    });
                }
            },
            error: function(xhr) {
                // Handle validation errors
                if (xhr.status === 422) {
                    try {
                        var errors = JSON.parse(xhr.responseText).errors;
                        $.each(errors, function(key, value) {
                            const fieldName = key.replace('t_berita.', '');
                            $(`#${fieldName}`).addClass('is-invalid');
                            $(`#${fieldName}_error`).html(value[0]);
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat memperbarui data'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbarui data'
                    });
                }
            },
            complete: function() {
                button.prop('disabled', false).html('Perbarui');
            }
        });
    });
    
    // Add validation handlers for input fields
    $('#fk_m_berita_dinamis, #berita_judul').on('change input', function() {
        $(this).removeClass('is-invalid');
        $(`#${this.id}_error`).html('');
    });
    
    $('input[name="t_berita[status_berita]"]').on('change', function() {
        $('#status_berita_error').html('');
    });
    
    // CSS for summernote validation
    $('<style>.note-editor.is-invalid {border: 1px solid #dc3545 !important;}</style>').appendTo('head');
});
</script>