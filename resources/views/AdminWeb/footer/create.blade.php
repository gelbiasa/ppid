<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Footer Baru</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formFooter" action="{{ url('/adminweb/footer/store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kategori Footer <span class="text-danger">*</span></label>
                            <select name="fk_m_kategori_footer" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoriFooters as $kategori)
                                    <option value="{{ $kategori->kategori_footer_id }}">
                                        {{ $kategori->kt_footer_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Judul Footer <span class="text-danger">*</span></label>
                            <input type="text" name="f_judul_footer" class="form-control" required 
                                   placeholder="Masukkan judul footer"
                                   maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL Footer <small class="text-muted">(Opsional)</small></label>
                            <input type="url" name="f_url_footer" class="form-control"
                                   placeholder="https://example.com (boleh tidak diisi)"
                                   maxlength="100">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ikon Footer <small class="text-muted">(Opsional)</small></label>
                            <div class="custom-file">
                                <input type="file" name="f_icon_footer" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Pilih file ikon (boleh tidak diisi)</label>
                            </div>
                            <small class="text-muted">Maksimal icon/gambar 2MB, Dengan fomat jpeg,png,jpg,gif,svg</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Custom file input label
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $('#formFooter').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Tutup modal
                    $('#myModal').modal('hide');
                    
                    // Tampilkan SweetAlert sukses
                    Swal.fire(
                        'Berhasil!',
                        response.message,
                        'success'
                    );
                    
                    // Reload DataTable
                    if (typeof footerTable !== 'undefined') {
                        footerTable.ajax.reload();
                    }
                } else {
                    // Jika validasi gagal atau ada kesalahan
                    if (response.errors) {
                        // Tampilkan error validasi
                        let errorMessage = '';
                        $.each(response.errors, function(field, messages) {
                            errorMessage += messages[0] + '<br>';
                        });
                        
                        Swal.fire(
                            'Gagal!',
                            errorMessage,
                            'error'
                        );
                    } else {
                        // Tampilkan pesan error dari server
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                }
            },
            error: function(xhr) {
                // Tangani kesalahan AJAX
                if (xhr.status === 422) {
                    // Error validasi
                    let errorMessage = '';
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        errorMessage += messages[0] + '<br>';
                    });
                    
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                } else {
                    // Error umum
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menyimpan data',
                        'error'
                    );
                }
            }
        });
    });
});
</script>