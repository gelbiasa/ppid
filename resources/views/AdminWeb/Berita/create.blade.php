<!-- View Create Berita (Form Tambah) -->
<div class="modal-header bg-primary text-white py-3">
    <h5 class="modal-title font-weight-bold">Tambah Berita Baru</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreateBerita" enctype="multipart/form-data">
        @csrf
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Informasi Dasar</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="fk_m_berita_dinamis" class="font-weight-medium">
                        Kategori Berita <span class="text-danger">*</span>
                    </label>
                    <select class="form-control form-control-lg" id="fk_m_berita_dinamis" name="t_berita[fk_m_berita_dinamis]">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($beritaDinamis as $kategori)
                            <option value="{{ $kategori->berita_dinamis_id }}">
                                {{ $kategori->bd_nama_submenu }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="fk_m_berita_dinamis_error"></div>
                </div>

                <div class="form-group">
                    <label for="berita_judul" class="font-weight-medium">
                        Judul Berita <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control form-control-lg" id="berita_judul" 
                           name="t_berita[berita_judul]" maxlength="140" 
                           placeholder="Masukkan judul berita...">
                    <div class="invalid-feedback" id="berita_judul_error"></div>
                </div>

                <div class="form-group">
                    <label for="status_berita" class="font-weight-medium">
                        Status Berita <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_aktif" name="t_berita[status_berita]" 
                                   class="custom-control-input" value="aktif">
                            <label class="custom-control-label" for="status_aktif">Aktif</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="status_nonaktif" name="t_berita[status_berita]" 
                                   class="custom-control-input" value="nonaktif">
                            <label class="custom-control-label" for="status_nonaktif">Nonaktif</label>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="status_berita_error"></div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Thumbnail</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="berita_thumbnail" class="font-weight-medium">
                                Thumbnail Berita
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="berita_thumbnail" 
                                       name="berita_thumbnail" accept="image/*">
                                <label class="custom-file-label" for="berita_thumbnail">Pilih file</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Format yang didukung: JPG, PNG, GIF. Ukuran maks 2.5 MB
                            </small>
                            <div class="invalid-feedback" id="berita_thumbnail_error"></div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <label for="berita_thumbnail_deskripsi" class="font-weight-medium">
                                Deskripsi Thumbnail
                            </label>
                            <textarea class="form-control" id="berita_thumbnail_deskripsi"
                                      name="t_berita[berita_thumbnail_deskripsi]" maxlength="255"
                                      placeholder="Contoh: Malang, 24 Maret 2025 - Penjelasan Singkat Informasi Berita"
                                      onkeyup="validateInput(this)" rows="3"></textarea>
                            <small class="form-text text-muted">Format: Lokasi, Tanggal - Deskripsi singkat berita (maksimal 255 karakter)</small>
                            <div class="text-danger error-message mt-1" id="berita_thumbnail_deskripsi_error"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="preview-container mt-md-4 text-center">
                            <div id="thumbnail-preview" class="mb-2">
                                <div class="placeholder-img d-flex justify-content-center align-items-center" 
                                     style="min-height: 150px; border: 2px dashed #ccc; border-radius: 8px;">
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p class="mb-0">Preview Thumbnail</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 font-weight-bold">Konten Berita</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="berita_deskripsi" class="font-weight-medium">
                        Konten Berita <span class="text-danger">*</span>
                    </label>
                    <textarea id="berita_deskripsi" name="t_berita[berita_deskripsi]" 
                              class="form-control summernote"></textarea>
                    <div class="invalid-feedback" id="berita_deskripsi_error"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer bg-light py-3">
    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
        <i class="fas fa-times mr-1"></i> Batal
    </button>
    <button type="button" class="btn btn-success px-4" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>

<script>
// Definisikan fungsi validateInput di luar fungsi ready
function validateInput(element) {
    const value = element.value.trim();
    
    if (value.length > 255) {
        $('#' + element.id + '_error').html('Deskripsi terlalu panjang (maksimal 255 karakter)');
    } else {
        $('#' + element.id + '_error').html('');
    }
}

$(document).ready(function() {
    // Setup AJAX CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Custom file input label
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        
        // Preview thumbnail if file is selected
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnail-preview').html(`
                    <img src="${e.target.result}" class="img-fluid rounded shadow-sm" 
                         style="max-height: 150px; object-fit: cover;">
                `);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Initialize Summernote dengan opsi yang lebih lengkap
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
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '28', '36', '48', '72'],
        lineHeights: ['0.8', '1.0', '1.2', '1.4', '1.5', '1.6', '1.8', '2.0', '3.0'],
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
                console.error(xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengunggah gambar'
                });
            }
        });
    }
    
    // Validasi semua field sebelum submit
    function validateForm() {
        let isValid = true;
        
        // Reset error messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback, .error-message').html('');
        
        // Validasi kategori
        const kategori = $('#fk_m_berita_dinamis').val();
        if (!kategori) {
            $('#fk_m_berita_dinamis').addClass('is-invalid');
            $('#fk_m_berita_dinamis_error').html('Kategori berita wajib dipilih');
            isValid = false;
        }
        
        // Validasi judul
        const judul = $('#berita_judul').val().trim();
        if (!judul) {
            $('#berita_judul').addClass('is-invalid');
            $('#berita_judul_error').html('Judul berita wajib diisi');
            isValid = false;
        } else if (judul.length > 140) {
            $('#berita_judul').addClass('is-invalid');
            $('#berita_judul_error').html('Judul berita maksimal 140 karakter');
            isValid = false;
        }
        
        // Validasi status
        const status = $('input[name="t_berita[status_berita]"]:checked').val();
        if (!status) {
            $('#status_berita_error').html('Status berita wajib dipilih');
            isValid = false;
        }
        
        // Validasi deskripsi thumbnail
        const thumbnailDesc = $('#berita_thumbnail_deskripsi').val().trim();
        if (thumbnailDesc.length > 255) {
            $('#berita_thumbnail_deskripsi_error').html('Deskripsi thumbnail maksimal 255 karakter');
            isValid = false;
        }
        
        // Validasi konten berita (Summernote)
        const summernoteContent = $('#berita_deskripsi').summernote('code');
        if (!summernoteContent || summernoteContent === '' || summernoteContent === '<p><br></p>' || summernoteContent === '<p>Tulis konten berita di sini...</p>') {
            $('#berita_deskripsi').next('.note-editor').addClass('is-invalid');
            $('#berita_deskripsi_error').html('Konten berita wajib diisi');
            isValid = false;
        }
        
        return isValid;
    }

    // Form submission
    $('#btnSubmitForm').on('click', function() {
        // Validasi form
        if (!validateForm()) {
            // Scroll to first error
            const firstError = $('.is-invalid:first');
            if (firstError.length) {
                $('.modal-body').animate({
                    scrollTop: firstError.offset().top - 200
                }, 500);
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda'
            });
            
            return false;
        }
        
        // Prepare form data
        const form = $('#formCreateBerita');
        const formData = new FormData(form[0]);
        const button = $(this);
        
        // Perbaikan untuk summernote content
        let summernoteContent = $('#berita_deskripsi').summernote('code');
        
        // Pastikan konten tidak kosong atau default
        if (summernoteContent === '<p>Tulis konten berita di sini...</p>') {
            Swal.fire({
                icon: 'error',
                title: 'Konten Kosong',
                text: 'Mohon isi konten berita terlebih dahulu'
            });
            return false;
        }
            
        // Update Summernote content
        formData.set('t_berita[berita_deskripsi]', summernoteContent);

        // Disable submit button and show loading
        button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

        $.ajax({
            url: '{{ url("adminweb/berita/createData") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                
                if (response.status) {
                    // Close modal and reload table
                    $('#myModal').modal('hide');
                    loadBeritaData(1, '');

                    // Show success message
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
                            
                            // Penanganan khusus untuk berita_deskripsi
                            if (fieldName === 'berita_deskripsi') {
                                $('#berita_deskripsi').next('.note-editor').addClass('is-invalid');
                                $('#berita_deskripsi_error').html(value[0]);
                            } else {
                                $(`#${fieldName}`).addClass('is-invalid');
                                $(`#${fieldName}_error`).html(value[0]);
                            }
                        });

                        // Scroll to first error
                        const firstError = $('.is-invalid:first');
                        if (firstError.length) {
                            $('.modal-body').animate({
                                scrollTop: firstError.offset().top - 200
                            }, 500);
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Mohon periksa kembali input Anda'
                        });
                    }
                }
            },
            error: function(xhr) {
                console.error("Error Response:", xhr.responseText);
                
                // Coba parse response jika JSON
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.errors) {
                        $.each(errorResponse.errors, function(key, value) {
                            const fieldName = key.replace('t_berita.', '');
                            $(`#${fieldName}`).addClass('is-invalid');
                            $(`#${fieldName}_error`).html(value[0]);
                        });
                    }
                } catch (e) {
                    // Jika bukan JSON, tampilkan error umum
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data: ' + xhr.responseText
                    });
                }
            },
            complete: function() {
                // Restore submit button
                button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
            }
        });
    });

    // Tambahkan validasi on input untuk field wajib
    $('#berita_judul').on('input', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
            $('#berita_judul_error').html('');
        }
    });
    
    $('#fk_m_berita_dinamis').on('change', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
            $('#fk_m_berita_dinamis_error').html('');
        }
    });
    
    $('input[name="t_berita[status_berita]"]').on('change', function() {
        $('#status_berita_error').html('');
    });
    
    // Tambahkan CSS untuk validasi error pada summernote
    $('<style>.note-editor.is-invalid {border: 1px solid #dc3545 !important;}</style>').appendTo('head');
});
</script>