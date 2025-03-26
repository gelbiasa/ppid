if (typeof BASE_URL === 'undefined') {
    var BASE_URL = window.location.origin;
}

function aturSummernote(selector = '#content', urlUnggahGambar = null, urlHapusGambar = null, opsi = {}) {
    const $elemen = $(selector);
    
    if (!$elemen.length || $elemen.hasClass('note-editor')) {
        return;
    }
    
    console.log('Menginisialisasi Summernote untuk', selector);
    
    // Tentukan konteks untuk URL unggah/hapus gambar
    let konteks = '';
    const path = window.location.pathname;
    
    if (path.includes('/SistemInformasi/KetentuanPelaporan')) {
        konteks = '/SistemInformasi/KetentuanPelaporan';
    } else if (path.includes('/AdminWeb/Pengumuman')) {
        konteks = '/AdminWeb/Pengumuman';
    }
    
    // Atur URL default berdasarkan konteks jika tidak disediakan
    if (!urlUnggahGambar) {
        urlUnggahGambar = BASE_URL + konteks + '/uploadImage';
    }
    if (!urlHapusGambar) {
        urlHapusGambar = BASE_URL + konteks + '/removeImage';
    }
    
    // Opsi default Summernote
    const opsiDefault = {
        placeholder: 'Tuliskan konten di sini...',
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
                for (let i = 0; i < files.length; i++) {
                    unggahGambarSummernote(files[i], this, urlUnggahGambar);
                }
            },
            onMediaDelete: function(target) {
                const urlGambar = target[0].src;
                hapusGambarSummernote(urlGambar, this, urlHapusGambar);
            }
        }
    };
    
    // Gabungkan opsi default dengan opsi kustom
    const opsiGabungan = { ...opsiDefault, ...opsi };
    
    // Inisialisasi Summernote
    $elemen.summernote(opsiGabungan);
}

function unggahGambarSummernote(file, editor, urlUnggah) {
    console.log('fungsi unggahGambar dipanggil');
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: urlUnggah,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        async: true,
        beforeSend: function() {
            $(".modal-dialog").addClass("enable-scroll");
        },
        success: function(response) {
            if (response.success) {
                $(editor).summernote('insertImage', response.data.url);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Gagal mengunggah gambar'
                });
            }
        },
        error: function(xhr) {
            console.error('Error saat mengunggah gambar:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat mengunggah gambar. Silakan coba lagi.'
            });
        },
        complete: function() {
            setTimeout(function() {
                $(".modal-dialog").removeClass("enable-scroll").css("overflow", "auto");
                $(".modal-body").css("overflow-y", "auto");
            }, 100);
        }
    });
}


function hapusGambarSummernote(urlGambar, editor, urlHapus) {
    console.log('fungsi hapusGambar dipanggil');
    $.ajax({
        url: urlHapus,
        method: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'url': urlGambar
        },
        success: function(response) {
            if (response.success) {
                console.log('Gambar berhasil dihapus dari server:', response.message);
            } else {
                console.error('Gagal menghapus gambar:', response.message);
            }
        },
        error: function(xhr) {
            console.error('Error saat menghapus gambar:', xhr);
        }
    });
}

/**
 * Mengatur form modal yang menyertakan Summernote
 * Fungsi ini harus dipanggil ketika modal ditampilkan
 */
function aturFormModal() {
    console.log('fungsi aturFormModal dipanggil');

    // Inisialisasi custom file input jika tersedia
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
    
    // Inisialisasi Summernote berdasarkan elemen yang ada
    if ($('#kp_konten').length) {
        aturSummernote('#kp_konten', null, null, {
            placeholder: 'Tuliskan konten ketentuan pelaporan di sini...'
        });
    }
    
    if ($('#konten').length) {
        aturSummernote('#konten', null, null, {
            placeholder: 'Tuliskan konten pengumuman di sini...'
        });
    }
}


function tampilkanFieldBerdasarkanTipe(tipe) {
    console.log('fungsi tampilkanFieldBerdasarkanTipe dipanggil dengan tipe:', tipe);

    // Sembunyikan semua container
    $('#judul_container, #thumbnail_container, #url_container, #file_container, #konten_container').hide();

    // Tampilkan container berdasarkan tipe
    if (tipe) {
        switch (tipe) {
            case 'link':
                $('#url_container').show();
                break;
            case 'file':
                $('#judul_container, #thumbnail_container, #file_container').show();
                break;
            case 'konten':
                $('#judul_container, #thumbnail_container, #konten_container').show();
                break;
        }
    }
}

$(document).ready(function() {
    console.log('Document ready - Summernote init');

    // Hapus error validasi saat input berubah
    $(document).on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        const errorId = `#${$(this).attr('id')}_error`;
        $(errorId).html('');
    });

    // Tangani pratinjau thumbnail
    $(document).on('change', '#thumbnail', function(e) {
        console.log('Thumbnail berubah');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnail_image').attr('src', e.target.result);
                $('#thumbnail_preview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#thumbnail_preview').hide();
        }
    });

    // Tangani perubahan tipe pengumuman
    $(document).on('change', '#tipe_pengumuman', function() {
        const tipe = $(this).val();
        console.log('tipe_pengumuman berubah menjadi:', tipe);
        tampilkanFieldBerdasarkanTipe(tipe);
    });

    // Event modal
    $(document).on('show.bs.modal', '.modal', function() {
        console.log('Event modal show dipanggil untuk:', this.id);
    });

    $(document).on('shown.bs.modal', '.modal', function() {
        console.log('Event modal shown dipanggil untuk:', this.id);
        aturFormModal();
        
        // Tangani visibilitas field berdasarkan tipe
        setTimeout(function() {
            const tipeSaatIni = $('#tipe_pengumuman').val();
            if (tipeSaatIni) {
                tampilkanFieldBerdasarkanTipe(tipeSaatIni);
            }
            $('#tipe_pengumuman').trigger('change');
        }, 300);
    });

    // Inisialisasi jika modal sudah terbuka
    if ($('.modal.show').length > 0) {
        console.log('Modal sudah terbuka, sedang menginisialisasi');
        aturFormModal();
    }
});