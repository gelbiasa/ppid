<div class="modal-header">
     <h5 class="modal-title">
         Edit Berita {{ $berita->berita_type == 'file' ? 'File' : 'Link' }}
     </h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <form id="formUpdateBerita" enctype="multipart/form-data">
     @csrf
     <input type="hidden" name="t_berita[berita_type]" value="{{ $berita->berita_type }}">
 
     <div class="modal-body">
         <div class="form-group">
             <label for="fk_m_berita_dinamis">Kategori Berita <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_m_berita_dinamis" name="t_berita[fk_m_berita_dinamis]">
                 <option value="">-- Pilih Kategori --</option>
                 @foreach($beritaDinamis as $kategori)
                     <option value="{{ $kategori->berita_dinamis_id }}" 
                         {{ $berita->fk_m_berita_dinamis == $kategori->berita_dinamis_id ? 'selected' : '' }}>
                         {{ $kategori->bd_nama_submenu }}
                     </option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="fk_m_berita_dinamis_error"></div>
         </div>
 
         @if($berita->berita_type == 'file')
             <div class="form-group">
                 <label for="berita_judul">Judul Berita <span class="text-danger">*</span></label>
                 <input type="text" class="form-control" id="berita_judul" 
                        name="t_berita[berita_judul]" maxlength="255"
                        value="{{ $berita->berita_judul }}">
                 <div class="invalid-feedback" id="berita_judul_error"></div>
             </div>
 
             <div class="form-group">
                 <label for="berita_thumbnail">Thumbnail Berita</label>
                 <div class="custom-file">
                     <input type="file" class="custom-file-input" id="berita_thumbnail" 
                            name="berita_thumbnail" accept="image/*">
                     <label class="custom-file-label" for="berita_thumbnail">
                         {{ $berita->berita_thumbnail ? basename($berita->berita_thumbnail) : 'Pilih gambar' }}
                     </label>
                 </div>
                 <small class="form-text text-muted">Ukuran maks 2.5 MB</small>
                 @if($berita->berita_thumbnail)
                     <div class="mt-2">
                         <img src="{{ asset('storage/' . $berita->berita_thumbnail) }}" 
                              alt="Thumbnail" 
                              class="img-thumbnail" 
                              style="max-width: 200px;">
                     </div>
                 @endif
                 <div class="invalid-feedback" id="berita_thumbnail_error"></div>
             </div>
 
             <div class="form-group">
                 <label for="berita_thumbnail_deskripsi">Deskripsi Thumbnail</label>
                 <input type="text" class="form-control" id="berita_thumbnail_deskripsi" 
                        name="t_berita[berita_thumbnail_deskripsi]" maxlength="255"
                        value="{{ $berita->berita_thumbnail_deskripsi }}">
                 <div class="invalid-feedback" id="berita_thumbnail_deskripsi_error"></div>
             </div>
 
             <div class="form-group">
                 <label for="berita_deskripsi">Konten Berita <span class="text-danger">*</span></label>
                 <textarea id="berita_deskripsi" name="t_berita[berita_deskripsi]" class="form-control">
                     {!! $berita->berita_deskripsi !!}
                 </textarea>
                 <div class="invalid-feedback" id="berita_deskripsi_error"></div>
             </div>
         @else
             <div class="form-group">
                 <label for="berita_link">Link Berita <span class="text-danger">*</span></label>
                 <input type="url" class="form-control" id="berita_link" 
                        name="t_berita[berita_link]" placeholder="https://contoh.com"
                        value="{{ $berita->berita_link }}">
                 <div class="invalid-feedback" id="berita_link_error"></div>
             </div>
         @endif
 
         <div class="form-group">
             <label for="status_berita">Status Berita <span class="text-danger">*</span></label>
             <select class="form-control" id="status_berita" name="t_berita[status_berita]">
                 <option value="aktif" {{ $berita->status_berita == 'aktif' ? 'selected' : '' }}>Aktif</option>
                 <option value="nonaktif" {{ $berita->status_berita == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
             </select>
             <div class="invalid-feedback" id="status_berita_error"></div>
         </div>
     </div>
     
     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
         <button type="submit" class="btn btn-primary" id="btnSubmitForm">
             <i class="fas fa-save mr-1"></i> Simpan Perubahan
         </button>
     </div>
 </form>
 
 <!-- Include Summernote CSS/JS -->
 <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
 <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
 
 <script>
     $(document).ready(function() {
         // Custom file input
         $(".custom-file-input").on("change", function() {
             var fileName = $(this).val().split("\\").pop();
             $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
         });
 
         @if($berita->berita_type == 'file')
             // Initialize Summernote for file type
             $('#berita_deskripsi').summernote({
                 placeholder: 'Tuliskan konten berita di sini...',
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
                     onImageUpload: function(files) {
                         uploadImage(files[0]);
                     }
                 }
             });
 
             function uploadImage(file) {
                 const formData = new FormData();
                 formData.append('image', file);
                 formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
 
                 $.ajax({
                     url: '{{ url("adminweb/upload-berita/uploadImage") }}',
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
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal',
                             text: 'Terjadi kesalahan saat mengunggah gambar'
                         });
                     }
                 });
             }
         @endif
 
         // Form submission
         $('#formUpdateBerita').on('submit', function(e) {
             e.preventDefault();
             
             // Reset error messages
             $('.is-invalid').removeClass('is-invalid');
             $('.invalid-feedback').html('');
 
             const form = $(this)[0];
             const formData = new FormData(form);
             const button = $('#btnSubmitForm');
 
             @if($berita->berita_type == 'file')
                 // Update textarea with Summernote content for file type
                 formData.set('t_berita[berita_deskripsi]', $('#berita_deskripsi').summernote('code'));
             @endif
 
             button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
 
             $.ajax({
                 url: '{{ url("adminweb/upload-berita/updateData/{$berita->berita_id}") }}',
                 type: 'POST',
                 data: formData,
                 processData: false,
                 contentType: false,
                 success: function (response) {
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
                             $.each(response.errors, function (key, value) {
                                 // Untuk t_berita fields
                                 if (key.startsWith('t_berita.')) {
                                     const fieldName = key.replace('t_berita.', '');
                                     $(`#${fieldName}`).addClass('is-invalid');
                                     $(`#${fieldName}_error`).html(value[0]);
                                 } else {
                                     // Untuk field lainnya
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