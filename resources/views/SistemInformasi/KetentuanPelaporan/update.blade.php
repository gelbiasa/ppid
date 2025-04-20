@php
  use App\Models\Website\WebMenuModel;
  $ketentuanPelaporanUrl = WebMenuModel::getDynamicMenuUrl('management-level');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Ketentuan Pelaporan</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <form id="formUpdateKetentuanPelaporan"
        action="{{ url($ketentuanPelaporanUrl . '/updateData/' . $ketentuanPelaporan->ketentuan_pelaporan_id) }}"
        method="POST">
        @csrf

        <div class="form-group">
            <label for="kategori_form">Kategori Form <span class="text-danger">*</span></label>
            <select class="form-control" id="kategori_form" name="m_ketentuan_pelaporan[fk_m_kategori_form]">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoriForms as $kategori)
                    <option value="{{ $kategori->kategori_form_id }}" {{ $ketentuanPelaporan->fk_m_kategori_form == $kategori->kategori_form_id ? 'selected' : '' }}>
                        {{ $kategori->kf_nama }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback" id="kategori_form_error"></div>
        </div>

        <div class="form-group">
            <label for="kp_judul">Judul Ketentuan <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="kp_judul" name="m_ketentuan_pelaporan[kp_judul]" maxlength="100"
                value="{{ $ketentuanPelaporan->kp_judul }}">
            <div class="invalid-feedback" id="kp_judul_error"></div>
        </div>

        <div class="form-group">
            <label for="kp_konten">Konten Ketentuan <span class="text-danger">*</span></label>
            <textarea id="kp_konten" name="m_ketentuan_pelaporan[kp_konten]"
                class="form-control">{{ $ketentuanPelaporan->kp_konten }}</textarea>
            <div class="invalid-feedback" id="kp_konten_error"></div>
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
        // Handle submit form
        $(document).on('click', '#btnSubmitForm', function () {
            console.log('Tombol submit diklik');
            // Reset semua error
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');
            $('.note-editor').removeClass('border border-danger');

            const form = $('#formUpdateKetentuanPelaporan');
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
                    button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                }
            });
        });
    });
</script>