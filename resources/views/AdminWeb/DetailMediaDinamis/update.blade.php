
        <div class="modal-header">
            <h5 class="modal-title" id="mediaDetailModalLabel">Edit Detail Media Dinamis</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form id="form-update-detail-media" enctype="multipart/form-data" aria-labelledby="mediaDetailModalLabel">
            @csrf
            <input type="hidden" name="detail_media_dinamis_id" value="{{ $detailMediaDinamis->detail_media_dinamis_id }}">
            
            <div class="modal-body" role="form">
                <div class="form-group">
                    <label for="fk_m_media_dinamis">Kategori Media <span class="text-danger">*</span></label>
                    <select class="form-control" id="fk_m_media_dinamis" 
                            name="t_detail_media_dinamis[fk_m_media_dinamis]" 
                            required 
                            aria-describedby="error-t_detail_media_dinamis.fk_m_media_dinamis">
                        <option value="">Pilih Kategori Media</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->media_dinamis_id }}" 
                                {{ $detailMediaDinamis->fk_m_media_dinamis == $kategori->media_dinamis_id ? 'selected' : '' }}>
                                {{ $kategori->md_kategori_media }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="error-t_detail_media_dinamis.fk_m_media_dinamis"></div>
                </div>
                
                <div class="form-group">
                    <label for="dm_judul_media">Judul Media <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="dm_judul_media" 
                           name="t_detail_media_dinamis[dm_judul_media]" 
                           value="{{ $detailMediaDinamis->dm_judul_media }}"
                           placeholder="Masukkan judul media" 
                           required 
                           maxlength="100"
                           aria-describedby="error-t_detail_media_dinamis.dm_judul_media">
                    <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_judul_media"></div>
                </div>
                
                <div class="form-group">
                    <label for="dm_type_media">Tipe Media <span class="text-danger">*</span></label>
                    <select class="form-control" id="dm_type_media" 
                            name="t_detail_media_dinamis[dm_type_media]" 
                            required 
                            onchange="toggleMediaInput()"
                            aria-describedby="error-t_detail_media_dinamis.dm_type_media">
                        <option value="">Pilih Tipe Media</option>
                        <option value="file" {{ $detailMediaDinamis->dm_type_media == 'file' ? 'selected' : '' }}>File</option>
                        <option value="link" {{ $detailMediaDinamis->dm_type_media == 'link' ? 'selected' : '' }}>Link</option>
                    </select>
                    <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_type_media"></div>
                </div>
                
                <div id="media-file-input" class="form-group" style="display: {{ $detailMediaDinamis->dm_type_media == 'file' ? 'block' : 'none' }};">
                    <label for="media_file">File Media</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="media_file" 
                               name="media_file" 
                               accept="image/*,application/pdf"
                               aria-describedby="media-file-help error-media_file">
                        <label class="custom-file-label" for="media_file">
                            {{ $detailMediaDinamis->dm_type_media == 'file' ? basename($detailMediaDinamis->dm_media_upload) : 'Pilih file' }}
                        </label>
                    </div>
                    <small id="media-file-help" class="form-text text-muted">Format: JPG, JPEG, PNG, GIF, SVG, WEBP, PDF (maks 2.5MB)</small>
                    <div class="invalid-feedback" id="error-media_file"></div>
                    
                    @if($detailMediaDinamis->dm_type_media == 'file')
                        <div id="current-file" class="mt-2">
                            <p>File saat ini:</p>
                            @php
                                $ext = pathinfo($detailMediaDinamis->dm_media_upload, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                            @endphp
                            
                            @if($isImage)
                                <img src="{{ asset('storage/' . $detailMediaDinamis->dm_media_upload) }}" 
                                     alt="Current File" class="img-thumbnail" style="max-height: 150px;">
                            @else
                                <a href="{{ asset('storage/' . $detailMediaDinamis->dm_media_upload) }}" 
                                   target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file"></i> {{ basename($detailMediaDinamis->dm_media_upload) }}
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <div id="file-preview" class="mt-2 d-none">
                        <p>Preview file baru:</p>
                        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                    
                    <input type="hidden" name="t_detail_media_dinamis[dm_media_upload]" 
                           value="{{ $detailMediaDinamis->dm_media_upload }}">
                </div>
                
                <div id="media-link-input" class="form-group" 
                     style="display: {{ $detailMediaDinamis->dm_type_media == 'link' ? 'block' : 'none' }};">
                    <label for="dm_media_upload_link">Link Media <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="dm_media_upload_link" 
                           name="t_detail_media_dinamis[dm_media_upload]" 
                           placeholder="https://example.com/media"
                           value="{{ $detailMediaDinamis->dm_type_media == 'link' ? $detailMediaDinamis->dm_media_upload : '' }}"
                           aria-describedby="error-t_detail_media_dinamis.dm_media_upload">
                    <div class="invalid-feedback" id="error-t_detail_media_dinamis.dm_media_upload"></div>
                </div>
                
                <div class="form-group">
                    <label for="status_media">Status Media <span class="text-danger">*</span></label>
                    <select class="form-control" id="status_media" 
                            name="t_detail_media_dinamis[status_media]" 
                            required
                            aria-describedby="error-t_detail_media_dinamis.status_media">
                        <option value="aktif" {{ $detailMediaDinamis->status_media == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $detailMediaDinamis->status_media == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <div class="invalid-feedback" id="error-t_detail_media_dinamis.status_media"></div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-update">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMediaInput() {
        const mediaType = $('#dm_type_media').val();
        
        if (mediaType === 'file') {
            $('#media-file-input').show();
            $('#media-link-input').hide();
            $('#dm_media_upload_link').removeAttr('required');
            $('#media_file').attr('required', false);
        } else if (mediaType === 'link') {
            $('#media-file-input').hide();
            $('#media-link-input').show();
            $('#dm_media_upload_link').attr('required', true);
            $('#media_file').removeAttr('required');
        } else {
            $('#media-file-input').hide();
            $('#media-link-input').hide();
        }
    }

    $(document).ready(function() {
        // Inisialisasi toggle media input saat halaman dimuat
        toggleMediaInput();

        // Update label file saat dipilih
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName);
        });

        // Preview file sebelum upload
        $('#media_file').on('change', function() {
            let file = this.files[0];
            
            if (file) {
                const fileReader = new FileReader();
                fileReader.onload = function(e) {
                    const fileType = file.type.split('/')[0];
                    if (fileType === 'image') {
                        $('#file-preview').removeClass('d-none');
                        $('#file-preview img').attr('src', e.target.result);
                    } else {
                        $('#file-preview').addClass('d-none');
                    }
                };
                fileReader.readAsDataURL(file);
            }
        });

        // Submit form via AJAX
        $('#form-update-detail-media').on('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.is-invalid').removeClass('is-invalid')
                             .removeAttr('aria-invalid');
            
            // Disable tombol untuk mencegah submit berulang
            $('#btn-update').attr('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...');
            
            // Buat FormData object untuk upload file
            var formData = new FormData(this);
            
            // Submit data form via AJAX
            $.ajax({
                url: '{{ url("adminweb/media-detail/updateData/{$detailMediaDinamis->detail_media_dinamis_id}") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#myModal').modal('hide');
                            reloadTable(); // Pastikan fungsi ini ada
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                    
                    // Enable kembali tombol
                    $('#btn-update').attr('disabled', false).html('Perbarui');
                },
                error: function(xhr) {
                    // Enable kembali tombol
                    $('#btn-update').attr('disabled', false).html('Perbarui');
                    
                    // Tangani error validasi
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            var $input = $('[name="' + key + '"]');
                            $input.addClass('is-invalid')
                                  .attr('aria-invalid', 'true');
                            $('#error-' + key).text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memperbarui data: ' + xhr.statusText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>