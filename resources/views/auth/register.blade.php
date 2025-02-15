<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

                <form method="POST" action="{{ url('register') }}" id="form-register">
                    @csrf

                    <!-- Pilih Level -->
                    <div class="input-group mb-3">
                        <select class="form-control" id="level_id" name="level_id" required>
                            <option value="4">Responden</option> 
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-users"></span>
                            </div>
                        </div>
                        <small id="error-level" class="error-text text-danger"></small>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <small id="error-username" class="error-text text-danger"></small>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <small id="error-nama" class="error-text text-danger"></small>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <small id="error-email" class="error-text text-danger"></small>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="Nomor Handphone"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <small id="error-no_hp" class="error-text text-danger"></small>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <small id="error-password" class="error-text text-danger"></small>

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
                    <small id="error-password_confirmation" class="error-text text-danger"></small>


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

    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Reset error messages when input changes
            $('input').on('input', function () {
                $(this).next('.input-group-append').next('.error-text').text('');
            });

            // Phone number validation
            $('#no_hp').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $("#form-register").on('submit', function (e) {
                e.preventDefault();
                $('.error-text').text(''); // Clear all previous error messages

                let isValid = true;
                const username = $('#username').val();
                const nama = $('#nama').val();
                const email = $('#email').val();
                const no_hp = $('#no_hp').val();
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();

                // Validation rules
                if (username.length < 4 || username.length > 20) {
                    $('#error-username').text('Username harus antara 4-20 karakter');
                    isValid = false;
                }

                if (nama.length < 2 || nama.length > 50) {
                    $('#error-nama').text('Nama harus antara 2-50 karakter');
                    isValid = false;
                }

                if (!email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
                    $('#error-email').text('Format email tidak valid');
                    isValid = false;
                }

                if (no_hp.length < 4 || no_hp.length > 15) {
                    $('#error-no_hp').text('Nomor handphone harus terdiri dari 4-15 digit');
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
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status) {
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
                                Object.keys(errors).forEach(function (key) {
                                    $(`#error-${key}`).text(errors[key][0]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: 'Registrasi gagal. Silakan coba lagi.'
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