<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Edit Kategori Footer</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="formEditKategoriFooter" action="{{ url('/adminweb/kategori-footer/'.$kategoriFooter['kategori_footer_id'].'/update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Kategori Footer <span class="text-danger">*</span></label>
                            <input type="text" name="kt_footer_kode" class="form-control" required 
                                   placeholder="Masukkan kode kategori" 
                                   value="{{ $kategoriFooter['kt_footer_kode'] }}"
                                   maxlength="20">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Kategori Footer <span class="text-danger">*</span></label>
                            <input type="text" name="kt_footer_nama" class="form-control" required 
                                   placeholder="Masukkan nama kategori"
                                   value="{{ $kategoriFooter['kt_footer_nama'] }}"
                                   maxlength="100">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formEditKategoriFooter').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST', // Laravel PUT method is simulated via POST
            data: formData,
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
                    if (typeof kategoriFooterTable !== 'undefined') {
                        kategoriFooterTable.ajax.reload();
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