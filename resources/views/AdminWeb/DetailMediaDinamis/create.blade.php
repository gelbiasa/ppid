<div class="modal-header">
    <h5 class="modal-title">Tambah Detail Media Dinamis</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formCreateDetailMediaDinamis" action="{{ url('adminweb/media-detail/createData') }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_m_media_dinamis">Kategori Media <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_media_dinamis" name="t_detail_media_dinamis[fk_m_media_dinamis]">
                <option value="">-- Pilih Kategori Media --</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->media_dinamis_id }}">
                        {{ $kategori->md_kategori_media }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="fk_m_media_dinamis_error"></div>
        </div>

        <div class="form-group">
            <label for="dm_judul_media">Judul Media <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="dm_judul_media" 
                   name="t_detail_media_dinamis[dm_judul_media]" 
                   maxlength="100">
            <div class="invalid-feedback" id="dm_judul_media_error"></div>
        </div>

        <div class="form-group">
            <label for="dm_type_media">Tipe Media <span class="text-danger">*</span></label>
            <select class="form-control" id="dm_type_media" name="t_detail_media_dinamis[dm_type_media]">
                <option value="">-- Pilih Tipe Media --</option>
                <option value="file">File</option>
                <option value="link">Link</option>
            </select>
            <div class="invalid-feedback" id="dm_type_media_error"></div>
        </div>

        <div class="form-group" id="fileUploadDiv" style="display: none;">
            <label for="media_file">File Media <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="media_file" name="media_file">
                <label class="custom-file-label" for="media_file">Pilih file</label>
            </div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, GIF, SVG, WEBP, PDF. Maks 2.5MB</small>
            <div class="invalid-feedback" id="media_file_error"></div>
        </div>

        <div class="form-group" id="linkUrlDiv" style="display: none;">
            <label for="dm_media_upload_link">URL Media <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="dm_media_upload_link" 
                   name="t_detail_media_dinamis[dm_media_upload]"
                   placeholder="https://...">
            <div class="invalid-feedback" id="dm_media_upload_error"></div>
        </div>

        <div class="form-group">
            <label for="status_media">Status Media <span class="text-danger">*</span></label>
            <select class="form-control" id="status_media" name="t_detail_media_dinamis[status_media]">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
            <div class="invalid-feedback" id="status_media_error"></div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-success" id="btnSubmitForm">
        <i class="fas fa-save mr-1"></i> Simpan
    </button>
</div>

<script>
$(document).ready(function() {
    // Handle dokumen tipe toggle menggunakan dropdown
    $('#dm_type_media').on('change', function() {
        const selectedType = $(this).val();
        if (selectedType === 'file') {
            $('#fileUploadDiv').show();
            $('#linkUrlDiv').hide();
            // Reset nilai link saat beralih ke file
            $('#dm_media_upload_link').val('');
        } else if (selectedType === 'link') {
            $('#fileUploadDiv').hide();
            $('#linkUrlDiv').show();
            // Reset file upload saat beralih ke link
            $('#media_file').val('');
            $('.custom-file-label').text('Pilih file');
        } else {
            $('#fileUploadDiv, #linkUrlDiv').hide();
        }
    });

    // Tampilkan nama file yang dipilih
    $('#media_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih file');
    });

    // Hapus error ketika input berubah
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
    });

    // Handle submit form
    $('#btnSubmitForm').on('click', function() {
        // Reset semua error
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        const form = $('#formCreateDetailMediaDinamis');
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
            success: function(response) {
                if (response.success) {
                    $('#myModal').modal('hide');
                    reloadTable();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                } else {
                    if (response.errors) {
                        // Tampilkan pesan error pada masing-masing field
                        $.each(response.errors, function(key, value) {
                            // Untuk t_detail_media_dinamis fields
                            if (key.startsWith('t_detail_media_dinamis.')) {
                                const fieldName = key.replace('t_detail_media_dinamis.', '');
                                $(`#${fieldName}`).addClass('is-invalid');
                                $(`#${fieldName}_error`).html(value[0]);
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
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                });
            },
            complete: function() {
                // Kembalikan tombol submit ke keadaan semula
                button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
            }
        });
    });
});
</script>