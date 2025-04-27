@php
  use App\Models\Website\WebMenuModel;
  $detailMediaUrlUrl = WebMenuModel::getDynamicMenuUrl('detail-media');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Detail Media Dinamis</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateDetailMediaDinamis" action="{{ url($detailMediaUrlUrl . '/updateData/' . $detailMediaDinamis->detail_media_dinamis_id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="fk_m_media_dinamis">Kategori Media <span class="text-danger">*</span></label>
            <select class="form-control" id="fk_m_media_dinamis" name="t_detail_media_dinamis[fk_m_media_dinamis]">
                <option value="">-- Pilih Kategori Media --</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->media_dinamis_id }}" 
                        {{ $detailMediaDinamis->fk_m_media_dinamis == $kategori->media_dinamis_id ? 'selected' : '' }}>
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
                   maxlength="100"
                   value="{{ $detailMediaDinamis->dm_judul_media }}">
            <div class="invalid-feedback" id="dm_judul_media_error"></div>
        </div>

        <div class="form-group">
            <label for="dm_type_media">Tipe Media <span class="text-danger">*</span></label>
            <select class="form-control" id="dm_type_media" name="t_detail_media_dinamis[dm_type_media]">
                <option value="file" {{ $detailMediaDinamis->dm_type_media == 'file' ? 'selected' : '' }}>File</option>
                <option value="link" {{ $detailMediaDinamis->dm_type_media == 'link' ? 'selected' : '' }}>Link</option>
            </select>
            <div class="invalid-feedback" id="dm_type_media_error"></div>
        </div>

        <div class="form-group" id="fileUploadDiv" {{ $detailMediaDinamis->dm_type_media == 'link' ? 'style=display:none;' : '' }}>
            <label for="media_file">File Media</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="media_file" name="media_file">
                <label class="custom-file-label" for="media_file">Pilih file baru</label>
            </div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, GIF, SVG, WEBP, PDF. Maks 2.5MB</small>
            <div class="invalid-feedback" id="media_file_error"></div>
            
            @if($detailMediaDinamis->dm_type_media == 'file')
            <p class="mb-1">File saat ini:</p>
                <a href="{{ Storage::url($detailMediaDinamis->dm_media_upload) }}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="fas fa-file-image"></i> Lihat Dokumen
                </a>
            @endif
        </div>

        <div class="form-group" id="linkUrlDiv" {{ $detailMediaDinamis->dm_type_media == 'file' ? 'style=display:none;' : '' }}>
            <label for="dm_media_upload_link">URL Media <span class="text-danger">*</span></label>
            <input type="url" class="form-control" id="dm_media_upload_link" 
                   name="t_detail_media_dinamis[dm_media_upload]"
                   placeholder="https://..."
                   value="{{ $detailMediaDinamis->dm_type_media == 'link' ? $detailMediaDinamis->dm_media_upload : '' }}">
            <div class="invalid-feedback" id="dm_media_upload_error"></div>
        </div>

        <div class="form-group">
            <label for="status_media">Status Media <span class="text-danger">*</span></label>
            <select class="form-control" id="status_media" name="t_detail_media_dinamis[status_media]">
                <option value="aktif" {{ $detailMediaDinamis->status_media == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $detailMediaDinamis->status_media == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
        if ($(this).val() === 'file') {
            $('#fileUploadDiv').show();
            $('#linkUrlDiv').hide();
            // Reset nilai link saat beralih ke file
            $('#dm_media_upload_link').val('');
        } else {
            $('#fileUploadDiv').hide();
            $('#linkUrlDiv').show();
            // Reset file upload saat beralih ke link
            $('#media_file').val('');
            $('.custom-file-label').text('Pilih file baru');
        }
    });

    // Tampilkan nama file yang dipilih
    $('#media_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih file baru');
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

        const form = $('#formUpdateDetailMediaDinamis');
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