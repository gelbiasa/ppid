<div class="modal-dialog modal-lg">
     <div class="modal-content">
         <div class="modal-header">
             <h4 class="modal-title">Edit Footer</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
         </div>
         <form id="formEditFooter" action="{{ url('/adminweb/footer/'.$footer->footer_id.'/update') }}" method="POST" enctype="multipart/form-data">
             @csrf
             @method('PUT')
             <div class="modal-body">
                 <div class="row">
                     <div class="col-md-6">
                         <div class="form-group">
                             <label>Kategori Footer <span class="text-danger">*</span></label>
                             <select name="fk_m_kategori_footer" class="form-control" required>
                                 <option value="">Pilih Kategori</option>
                                 @foreach($kategoriFooters as $kategori)
                                     <option value="{{ $kategori->kategori_footer_id }}"
                                         {{ $footer->fk_m_kategori_footer == $kategori->kategori_footer_id ? 'selected' : '' }}>
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
                                    value="{{ $footer->f_judul_footer }}">
                         </div>
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-md-6">
                         <div class="form-group">
                             <label>URL Footer</label>
                             <input type="url" name="f_url_footer" class="form-control" 
                                    placeholder="https://example.com"
                                    value="{{ $footer->f_url_footer }}">
                         </div>
                     </div>
                     <div class="col-md-6">
                         <div class="form-group">
                             <label>Ikon Footer</label>
                             <div class="custom-file">
                                 <input type="file" name="f_icon_footer" class="custom-file-input" id="customFile">
                                 <label class="custom-file-label" for="customFile">
                                     {{ $footer->f_icon_footer ? basename($footer->f_icon_footer) : 'Pilih file ikon' }}
                                 </label>
                             </div>
                             <small class="text-muted">Maksimal 2MB, format gambar</small>
                             
                             @if($footer->f_icon_footer)
                                 <div class="mt-2">
                                     <strong>Ikon Saat Ini:</strong>
                                     <img src="{{ asset('storage/'.$footer->f_icon_footer) }}" 
                                          class="img-thumbnail" style="max-width: 150px;">
                                 </div>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                 <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

    // Validasi form edit footer
    $("#formEditFooter").validate({
        rules: {
            fk_m_kategori_footer: {
                required: true
            },
            f_judul_footer: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            f_url_footer: {
                url: true
            },
            f_icon_footer: {
                extension: "png|jpg|jpeg|gif|svg",
                filesize: 2048000 // 2MB
            }
        },
        messages: {
            fk_m_kategori_footer: {
                required: "Kategori footer harus dipilih"
            },
            f_judul_footer: {
                required: "Judul footer harus diisi",
                minlength: "Judul footer minimal 3 karakter",
                maxlength: "Judul footer maksimal 255 karakter"
            },
            f_url_footer: {
                url: "URL tidak valid"
            },
            f_icon_footer: {
                extension: "Format gambar tidak valid",
                filesize: "Ukuran file maksimal 2MB"
            }
        },
        submitHandler: function(form) {
            var formData = new FormData(form);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST', // Laravel PUT method is simulated via POST with _method
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