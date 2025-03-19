<div class="modal-header">
    <h5 class="modal-title">Tambah Ketentuan Pelaporan Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreateKetentuanPelaporan" action="{{ url('SistemInformasi/KetentuanPelaporan/createData') }}"
        method="POST">
        @csrf

        <div class="form-group">
            <label for="kategori_form">Kategori Form <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_form" name="m_ketentuan_pelaporan[fk_m_kategori_form]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriForms as $kategori)
                    <option value="{{ $kategori->kategori_form_id }}">{{ $kategori->kf_nama }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_form_error"></div>
        </div>

        <div class="form-group">
            <label for="kp_judul">Judul Ketentuan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kp_judul" name="m_ketentuan_pelaporan[kp_judul]"
                maxlength="100">
            <div class="invalid-feedback" id="kp_judul_error"></div>
        </div>

        <div class="form-group">
            <label for="kp_konten">Konten Ketentuan <span class="text-danger">*</span></label>
            <textarea id="kp_konten" name="m_ketentuan_pelaporan[kp_konten]" class="form-control"></textarea>
            <div class="invalid-feedback" id="kp_konten_error"></div>
        </div>

    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-success" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>

<!-- Include Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Summernote
        setTimeout(function () {
            $('#kp_konten').summernote({
                placeholder: 'Tuliskan konten ketentuan pelaporan di sini...',
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
        }, 100);  // 100ms delay

        function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ url("SistemInformasi/KetentuanPelaporan/uploadImage") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        $('#kp_konten').summernote('insertImage', response.url);
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

            const form = $('#formCreateKetentuanPelaporan');
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
                        $('#table_ketentuan_pelaporan').DataTable().ajax.reload();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        if (response.errors) {
                            // Tampilkan pesan error pada masing-masing field
                            $.each(response.errors, function (key, value) {
                                // Untuk m_ketentuan_pelaporan fields
                                if (key.startsWith('m_ketentuan_pelaporan.')) {
                                    const fieldName = key.replace('m_ketentuan_pelaporan.', '');
                                    if (fieldName === 'fk_m_kategori_form') {
                                        $('#kategori_form').addClass('is-invalid');
                                        $('#kategori_form_error').html(value[0]);
                                    } else if (fieldName === 'kp_konten') {
                                        $('.note-editor').addClass('border border-danger');
                                        $('#kp_konten_error').html(value[0]).show();
                                    } else {
                                        $(`#${fieldName}`).addClass('is-invalid');
                                        $(`#${fieldName}_error`).html(value[0]);
                                    }
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                }
            });
        });

        // Function untuk upload gambar (jika dibutuhkan)
        function uploadImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ url("SistemInformasi/KetentuanPelaporan/uploadImage") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#kp_konten').summernote('insertImage', data.url);
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
    });
</script>