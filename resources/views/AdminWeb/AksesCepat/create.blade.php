@php
  use App\Models\Website\WebMenuModel;
  $detailAksesCepatUrl = WebMenuModel::getDynamicMenuUrl('detail-akses-cepat');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah Akses Cepat</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-akses-cepat" action="{{ url($detailAksesCepatUrl . '/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label for="fk_m_kategori_akses">Kategori Akses Cepat</label>
            <input type="hidden" name="t_akses_cepat[fk_m_kategori_akses]" value="{{ $kategoriAkses->kategori_akses_id }}">
            <input type="text" class="form-control" value="{{ $kategoriAkses->mka_judul_kategori }}" readonly>
            <div class="invalid-feedback" id="error-fk_m_kategori_akses"></div>
        </div>

        <div class="form-group">
            <label for="ac_judul">Judul Akses Cepat <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ac_judul" name="t_akses_cepat[ac_judul]" required maxlength="100">
            <div class="invalid-feedback" id="error-ac_judul"></div>
        </div>

        <div class="form-group">
            <label for="ac_url">URL Akses Cepat <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ac_url" name="t_akses_cepat[ac_url]" required maxlength="100" 
                placeholder="https://contoh.com">
            <div class="invalid-feedback" id="error-ac_url"></div>
            <small class="form-text text-muted">Masukkan URL lengkap dengan http:// atau https://</small>
        </div>

        <div class="form-group">
            <label for="ac_static_icon">Icon Statis Akses Cepat <span class="text-danger">*</span></label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="ac_static_icon" name="t_akses_cepat[ac_static_icon]" 
                    accept="image/*" required>
                <label class="custom-file-label" for="ac_static_icon">Pilih file</label>
            </div>
            <div class="invalid-feedback" id="error-ac_static_icon"></div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG, GIF. Ukuran maksimal: 2.5MB.</small>
            <div id="static-image-preview" class="mt-2 d-none">
                <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
            </div>
        </div>

        <div class="form-group">
            <label for="ac_animation_icon">Icon Hover Akses Cepat</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="ac_animation_icon" name="t_akses_cepat[ac_animation_icon]" 
                    accept="image/*">
                <label class="custom-file-label" for="ac_animation_icon">Pilih file</label>
            </div>
            <div class="invalid-feedback" id="error-ac_animation_icon"></div>
            <small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG, GIF. Ukuran maksimal: 2.5MB.</small>
            <div id="animation-image-preview" class="mt-2 d-none">
                <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
    </div>
</form>
<script>
    $(document).ready(function () {
        // Hapus error ketika input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('name').replace('t_akses_cepat[', '').replace(']', '')}_error`;
            $(errorId).html('');
        });

        // Reset form dan error state saat modal ditutup
        $('#myModal').on('hidden.bs.modal', function() {
            $('#form-create-akses-cepat')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('#static-image-preview, #animation-image-preview').addClass('d-none');
            $('.custom-file-label').text('Pilih file');
        });

        // Validasi dan preview ukuran file
        function validateAndPreviewFile(input, previewSelector, labelSelector) {
            const file = input[0].files[0];
            if (file) {
                const fileSizeMB = file.size / (1024 * 1024);
                $(labelSelector).text(
                    file.name + ' (' + fileSizeMB.toFixed(2) + ' MB)'
                );
                
                if (fileSizeMB > 3) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Ukuran file ' + fileSizeMB.toFixed(2) + ' MB melebihi batas 3MB',
                        icon: 'warning'
                    });
                    
                    input.val('');
                    $(labelSelector).text('Pilih file');
                    $(previewSelector).addClass('d-none');
                    return false;
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewSelector).removeClass('d-none');
                        $(previewSelector + ' img').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                    return true;
                }
            }
            return true;
        }

        // Preview static icon
        $('#ac_static_icon').on('change', function() {
            validateAndPreviewFile(
                $(this), 
                '#static-image-preview', 
                '.custom-file-label[for="ac_static_icon"]'
            );
        });

        // Preview animation icon
        $('#ac_animation_icon').on('change', function() {
            validateAndPreviewFile(
                $(this), 
                '#animation-image-preview', 
                '.custom-file-label[for="ac_animation_icon"]'
            );
        });

        // Handle submit form
        $('#form-create-akses-cepat').on('submit', function(e) {
            e.preventDefault();
            
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            
            const form = $(this);
            const formData = new FormData(this);
            const button = $('#btn-save');
            
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
                                // Hapus prefix 't_akses_cepat.' dari key
                                const cleanKey = key.replace('t_akses_cepat.', '');
                                $(`#${cleanKey}`).addClass('is-invalid');
                                $(`#${cleanKey}_error`).html(value[0]);
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