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
                                    placeholder="Masukkan judul footer">
                         </div>
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-md-6">
                         <div class="form-group">
                             <label>URL Footer</label>
                             <input type="url" name="f_url_footer" class="form-control" 
                                    placeholder="https://example.com">
                         </div>
                     </div>
                     <div class="col-md-6">
                         <div class="form-group">
                             <label>Ikon Footer</label>
                             <div class="custom-file">
                                 <input type="file" name="f_icon_footer" class="custom-file-input" id="customFile">
                                 <label class="custom-file-label" for="customFile">Pilih file ikon</label>
                             </div>
                             <small class="text-muted">Maksimal 2MB, format gambar</small>
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


    
        submitHandler: function(form) {
            var formData = new FormData(form);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        footerTable.ajax.reload(); // Reload DataTable
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: messages[0]
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>