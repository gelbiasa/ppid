if (typeof BASE_URL === 'undefined') {
    var BASE_URL = window.location.origin;
}

function aturSummernote(selector = '#content', urlUnggahGambar = null, urlHapusGambar = null, opsi = {}) {
    // Cari elemen dengan selector yang diberikan
    const $elemen = $(selector);
    
    // Periksa apakah elemen ditemukan dan belum diinisialisasi
    if (!$elemen.length) {
        console.warn('Elemen dengan selector', selector, 'tidak ditemukan');
        return;
    }
    
    // Periksa apakah elemen sudah diinisialisasi sebagai Summernote
    if ($elemen.next().hasClass('note-editor')) {
        console.log('Summernote sudah diinisialisasi untuk', selector);
        return;
    }
    
    console.log('Menginisialisasi Summernote untuk', selector);
    
    // Tentukan konteks untuk URL unggah/hapus gambar berdasarkan path
    let konteks = tentukanKonteks();
    
    // Atur URL default berdasarkan konteks jika tidak disediakan
    if (!urlUnggahGambar) {
        // Jika BASE_URL tidak diakhiri dengan slash, tambahkan
        const baseUrl = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
        urlUnggahGambar = baseUrl + konteks + '/uploadImage';
    }
    if (!urlHapusGambar) {
        // Jika BASE_URL tidak diakhiri dengan slash, tambahkan
        const baseUrl = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
        urlHapusGambar = baseUrl + konteks + '/removeImage';
    }
    
    console.log('URL Unggah Gambar:', urlUnggahGambar);
    console.log('URL Hapus Gambar:', urlHapusGambar);
    
    // Opsi default Summernote
    const opsiDefault = {
        placeholder: 'Tuliskan konten di sini...',
        tabsize: 2,
        height: 300,
        toolbar: [
            ['style', ['style']],
            // Menambahkan font family, font size, dan line height
            ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'clear']],
            // Menambahkan background color dan text color
            ['color', ['color', 'forecolor', 'backcolor']],
            ['para', ['ul', 'ol', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        // Menambahkan opsi untuk font sizes
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '28', '36', '48', '72'],
        // Menambahkan opsi untuk font families
        fontNames: [
            'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 
            'Tahoma', 'Times New Roman', 'Verdana', 'Roboto', 'Open Sans'
        ],
        // Menambahkan opsi untuk line heights
        lineHeights: ['0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],
        // Menambahkan opsi warna dengan recent color
        colors: [
            ['#000000', '#424242', '#636363', '#9C9C94', '#CEC6CE', '#EFEFEF', '#F7F7F7', '#FFFFFF'],
            ['#FF0000', '#FF9C00', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#9C00FF', '#FF00FF'],
            ['#F7C6CE', '#FFE7CE', '#FFEFC6', '#D6EFD6', '#CEDEE7', '#CEE7F7', '#D6D6E7', '#E7D6DE'],
            ['#E79C9C', '#FFC69C', '#FFE79C', '#B5D6A5', '#A5C6CE', '#9CC6EF', '#B5A5D6', '#D6A5BD'],
            ['#E76363', '#F7AD6B', '#FFD663', '#94BD7B', '#73A5AD', '#6BADDE', '#8C7BC6', '#C67BA5'],
            ['#CE0000', '#E79439', '#EFC631', '#6BA54A', '#4A7B8C', '#3984C6', '#634AA5', '#A54A7B'],
            ['#9C0000', '#B56308', '#BD9400', '#397B21', '#104A5A', '#085294', '#311873', '#731842'],
            ['#630000', '#7B3900', '#846300', '#295218', '#083139', '#003163', '#21104A', '#4A1031']
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
        },
        lang: 'id-ID' // Menggunakan bahasa Indonesia jika tersedia
    };
    
    // Gabungkan opsi default dengan opsi kustom
    const opsiGabungan = { ...opsiDefault, ...opsi };
    
    try {
        // Inisialisasi Summernote
        $elemen.summernote(opsiGabungan);
    } catch (error) {
        console.error('Error saat inisialisasi Summernote:', error);
    }
}

function tentukanKonteks() {
    let konteks = '';
    const path = window.location.pathname;
    
    // Cek konteks berdasarkan URL
    if (path.includes('ketentuan-pelaporan')) {
        konteks = 'ketentuan-pelaporan';
    } else if (path.includes('detail-pengumuman')) {
        konteks = 'detail-pengumuman';
    } else if (path.includes('detail-berita')) {
        konteks = 'detail-berita';
    }
    
    // Jika tidak ditemukan konteks spesifik, gunakan fallback
    if (!konteks) {
        // Cek apakah path mengandung urutan folder yang dapat digunakan sebagai konteks
        const pathSegments = path.split('/').filter(segment => segment.length > 0);
        if (pathSegments.length >= 1) {
            // Gunakan segment terakhir yang bermakna sebagai konteks
            konteks = pathSegments[pathSegments.length - 1];
        } else {
            // Fallback ke 'default'
            konteks = 'default';
        }
    }
    
    console.log('Konteks terdeteksi:', konteks);
    return konteks;
}

function unggahGambarSummernote(file, editor, urlUnggah) {
    console.log('Mengunggah gambar...');
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    try {
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
                    // Memeriksa struktur respons dan mendapatkan URL dengan benar
                    let imageUrl = '';
                    if (response.data && response.data.url) {
                        // Format respons dari BaseControllerFunction.jsonSuccess
                        imageUrl = response.data.url;
                    } else if (response.url) {
                        // Format respons lama
                        imageUrl = response.url;
                    }
                    
                    // Pastikan ada URL yang valid sebelum menyisipkan gambar
                    if (imageUrl) {
                        $(editor).summernote('insertImage', imageUrl);
                    } else {
                        console.error('URL gambar tidak ditemukan dalam respons:', response);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Format respons tidak valid saat mengunggah gambar'
                        });
                    }
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
    } catch (error) {
        console.error('Error pada fungsi unggahGambarSummernote:', error);
    }
}

function hapusGambarSummernote(urlGambar, editor, urlHapus) {
    console.log('Menghapus gambar...');
    try {
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
    } catch (error) {
        console.error('Error pada fungsi hapusGambarSummernote:', error);
    }
}

/**
 * Inisialisasi form modal dengan Summernote
 * - Diperbaiki untuk menangani inisialisasi Summernote dengan lebih baik
 */
function aturFormModal() {
    console.log('Inisialisasi form modal...');

    try {
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
        
        if ($('#berita_deskripsi').length) {
            aturSummernote('#berita_deskripsi', null, null, {
                placeholder: 'Tuliskan konten berita di sini...'
            });
        }
        
        // Tangani visibilitas field berdasarkan tipe
        setTimeout(function() {
            const tipeSaatIni = $('#tipe_pengumuman').val();
            if (tipeSaatIni) {
                tampilkanFieldBerdasarkanTipe(tipeSaatIni);
            }
        }, 50);
    } catch (error) {
        console.error('Error pada fungsi aturFormModal:', error);
    }
}

function tampilkanFieldBerdasarkanTipe(tipe) {
    console.log('Mengatur tampilan field untuk tipe:', tipe);

    try {
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
                    // Pastikan Summernote diinisialisasi setelah container ditampilkan
                    if ($('#konten').length && $('#konten_container').is(':visible')) {
                        setTimeout(function() {
                            aturSummernote('#konten', null, null, {
                                placeholder: 'Tuliskan konten pengumuman di sini...'
                            });
                        }, 50);
                    }
                    break;
            }
        }
    } catch (error) {
        console.error('Error pada fungsi tampilkanFieldBerdasarkanTipe:', error);
    }
}

// Fungsi untuk memastikan modal telah benar-benar ditampilkan sebelum inisialisasi editor
function initializeSummernoteOnModalShown() {
    $('.modal').on('shown.bs.modal', function() {
        console.log('Modal telah ditampilkan sepenuhnya, menginisialisasi editor...');
        aturFormModal();
    });

    // Menerapkan event handler untuk mengatur jenis field ketika tipe pengumuman berubah
    $(document).on('change', '#tipe_pengumuman', function() {
        const tipe = $(this).val();
        console.log('Tipe pengumuman berubah menjadi:', tipe);
        tampilkanFieldBerdasarkanTipe(tipe);
    });
}

// Memperbaiki event handler saat dokumen dimuat
$(document).ready(function() {
    console.log('Summernote: Document ready');

    try {
        // Inisialisasi event handler untuk modal
        initializeSummernoteOnModalShown();
        
        // Hapus error validasi saat input berubah
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
            const errorId = `#${$(this).attr('id')}_error`;
            $(errorId).html('');
        });

        // Tangani pratinjau thumbnail
        $(document).on('change', '#thumbnail, #berita_thumbnail', function(e) {
            console.log('Thumbnail berubah');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const targetId = this.id === 'thumbnail' ? '#thumbnail_image' : '#thumbnail-preview img';
                    $(targetId).attr('src', e.target.result);
                    if (this.id === 'thumbnail') {
                        $('#thumbnail_preview').show();
                    } else {
                        $('#thumbnail-preview').removeClass('d-none');
                    }
                }.bind(this);
                reader.readAsDataURL(file);
            }
        });
        
        // Buat CSS untuk validasi Summernote
        if ($('style#summernote-validation-css').length === 0) {
            $('<style id="summernote-validation-css">.note-editor.is-invalid, .note-editor.border.border-danger {border: 1px solid #dc3545 !important;}</style>').appendTo('head');
        }
    } catch (error) {
        console.error('Error pada document ready:', error);
    }
});