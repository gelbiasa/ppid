<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Pengguna</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>Register</b>Yuk</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register to create an account</p>

                <form method="POST" action="{{ url('register') }}" id="form-register" enctype="multipart/form-data">
                    @csrf

                    <!-- Pilih Level -->
                    <div class="input-group mb-3">
                        <select class="form-control" id="level_id" name="m_user[fk_m_level]" required>
                            <option value="5">Responden</option> 
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-users"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-fk_m_level" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="text" id="nama_pengguna" name="m_user[nama_pengguna]" class="form-control" placeholder="Nama (sesuai KTP)" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-nama_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="email" id="email_pengguna" name="m_user[email_pengguna]" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-email_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="text" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" class="form-control" placeholder="Nomor Handphone" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-no_hp_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <textarea id="alamat_pengguna" name="m_user[alamat_pengguna]" class="form-control" placeholder="Alamat" required></textarea>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-map-marker-alt"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-alamat_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="text" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" class="form-control" placeholder="Pekerjaan" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-briefcase"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-pekerjaan_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="text" id="nik_pengguna" name="m_user[nik_pengguna]" class="form-control" placeholder="NIK (16 digit)" required maxlength="16">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-nik_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="upload_nik_pengguna" name="upload_nik_pengguna" required accept="image/jpeg,image/png,image/jpg">
                            <label class="custom-file-label" for="upload_nik_pengguna">Upload Foto KTP</label>
                        </div>
                    </div>
                    <small id="error-upload_nik_pengguna" class="error-text text-danger d-block"></small>

                    <div class="input-group mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-password" class="error-text text-danger d-block"></small>

                    <!-- Verifikasi Password -->
                    <div class="input-group mb-3">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-control" placeholder="Verifikasi Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-password_confirmation" class="error-text text-danger d-block"></small>

                    <div class="text-center mb-3">
                        <a href="{{ url('login') }}">Sudah Punya Akun?</a>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- bs-custom-file-input -->
    <script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();

            // Reset error messages when input changes
            $('input, select, textarea').on('input change', function () {
                const fieldName = $(this).attr('name');
                if (fieldName.includes('[')) {
                    // For fields with m_user[fieldname] format
                    const baseName = fieldName.split('[')[1].replace(']', '');
                    $('#error-' + baseName).text('');
                } else {
                    // For normal fields like password
                    $('#error-' + fieldName).text('');
                }
            });

            // Phone number validation
            $('#no_hp_pengguna').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // NIK validation (numbers only)
            $('#nik_pengguna').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $("#form-register").on('submit', function (e) {
                e.preventDefault();
                $('.error-text').text(''); // Clear all previous error messages

                let isValid = true;
                const nama_pengguna = $('#nama_pengguna').val().trim();
                const email_pengguna = $('#email_pengguna').val().trim();
                const no_hp_pengguna = $('#no_hp_pengguna').val().trim();
                const alamat_pengguna = $('#alamat_pengguna').val().trim();
                const pekerjaan_pengguna = $('#pekerjaan_pengguna').val().trim();
                const nik_pengguna = $('#nik_pengguna').val().trim();
                const upload_nik_pengguna = $('#upload_nik_pengguna').val();
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                // Validation rules
                if (nama_pengguna.length < 2 || nama_pengguna.length > 50) {
                    $('#error-nama_pengguna').text('Nama harus antara 2-50 karakter');
                    isValid = false;
                }

                if (!email_pengguna.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
                    $('#error-email_pengguna').text('Format email tidak valid');
                    isValid = false;
                }

                if (no_hp_pengguna.length < 4 || no_hp_pengguna.length > 15) {
                    $('#error-no_hp_pengguna').text('Nomor handphone harus terdiri dari 4-15 digit');
                    isValid = false;
                }

                if (alamat_pengguna.length < 5) {
                    $('#error-alamat_pengguna').text('Alamat terlalu pendek');
                    isValid = false;
                }

                if (pekerjaan_pengguna.length < 2) {
                    $('#error-pekerjaan_pengguna').text('Pekerjaan wajib diisi');
                    isValid = false;
                }

                if (nik_pengguna.length !== 16) {
                    $('#error-nik_pengguna').text('NIK harus terdiri dari 16 digit');
                    isValid = false;
                }

                if (!upload_nik_pengguna) {
                    $('#error-upload_nik_pengguna').text('Upload foto KTP wajib dilakukan');
                    isValid = false;
                }

                if (password.length < 5) {
                    $('#error-password').text('Password minimal harus 5 karakter');
                    isValid = false;
                }

                if (password !== confirmPassword) {
                    $('#error-password_confirmation').text('Verifikasi password tidak sesuai dengan password baru');
                    isValid = false;
                }

                if (isValid) {
                    // Create FormData object to handle file uploads
                    const formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Register Berhasil',
                                    text: response.message
                                }).then(function () {
                                    window.location = response.redirect;
                                });
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                // Handle error messages with the m_user prefix
                                Object.keys(errors).forEach(function (key) {
                                    if (key.startsWith('m_user.')) {
                                        // Convert m_user.field to just field for the error display
                                        const fieldName = key.split('.')[1];
                                        $(`#error-${fieldName}`).text(errors[key][0]);
                                    } else {
                                        $(`#error-${key}`).text(errors[key][0]);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: xhr.responseJSON?.message || 'Registrasi gagal. Silakan coba lagi.'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>