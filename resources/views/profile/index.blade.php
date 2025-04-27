@extends('layouts.template')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $page->title }}</h3>
                    </div>

                    <div class="card-body">
                        <!-- Tab Navigation -->
                        <div class="jarak-menu"></div>
                        <ul class="nav nav-menu_profil mb-0" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ session('error_type') ? '' : 'active' }}" id="data-tab"
                                    data-toggle="tab" href="#data-pengguna" role="tab" aria-controls="data-pengguna"
                                    aria-selected="true">Data Pengguna</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session('error_type') ? 'active' : '' }}" id="password-tab"
                                    data-toggle="tab" href="#ubah-password" role="tab" aria-controls="ubah-password"
                                    aria-selected="false">Ubah Password</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="profileTabContent">
                            <!-- Data Pengguna -->
                            <div class="tab-pane fade {{ session('error_type') ? '' : 'show active' }}" id="data-pengguna"
                                role="tabpanel" aria-labelledby="data-tab">
                                <div class="container border-container">
                                    <form id="profile-form" method="POST"
                                        action="{{ url('profile/update_pengguna', Auth::user()->user_id) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label for="hak_akses_nama">Level Pengguna</label>
                                            <input type="text" class="form-control" id="hak_akses_nama"
                                                value="{{ Auth::user()->level->hak_akses_nama }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="nama_pengguna">Nama</label>
                                            <input type="text"
                                                class="form-control @error('nama_pengguna') is-invalid @enderror"
                                                id="nama_pengguna" name="nama_pengguna"
                                                value="{{ old('nama_pengguna', Auth::user()->nama_pengguna) }}" required>
                                            @error('nama_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="nama_pengguna">Alias</label>
                                            <input type="text"
                                                class="form-control @error('alias') is-invalid @enderror"
                                                id="alias" name="alias"
                                                value="{{ session('alias') }}" disabled>
                                            @error('alias')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="alamat_pengguna">Alamat</label>
                                            <input type="text"
                                                class="form-control @error('alamat_pengguna') is-invalid @enderror"
                                                id="alamat_pengguna" name="alamat_pengguna"
                                                value="{{ old('alamat_pengguna', Auth::user()->alamat_pengguna) }}"
                                                required>
                                            @error('alamat_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="no_hp_pengguna">Nomor Handphone</label>
                                            <input type="text"
                                                class="form-control @error('no_hp_pengguna') is-invalid @enderror"
                                                id="no_hp_pengguna" name="no_hp_pengguna"
                                                value="{{ old('no_hp_pengguna', Auth::user()->no_hp_pengguna) }}" required>
                                            @error('no_hp_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email_pengguna">Email</label>
                                            <input type="email"
                                                class="form-control @error('email_pengguna') is-invalid @enderror"
                                                id="email_pengguna" name="email_pengguna"
                                                value="{{ old('email_pengguna', Auth::user()->email_pengguna) }}" required>
                                            @error('email_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="pekerjaan_pengguna">Pekerjaan</label>
                                            <input type="text"
                                                class="form-control @error('pekerjaan_pengguna') is-invalid @enderror"
                                                id="pekerjaan_pengguna" name="pekerjaan_pengguna"
                                                value="{{ old('pekerjaan_pengguna', Auth::user()->pekerjaan_pengguna) }}"
                                                required>
                                            @error('pekerjaan_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="nik_pengguna">NIK</label>
                                            <input type="text"
                                                class="form-control @error('nik_pengguna') is-invalid @enderror"
                                                id="nik_pengguna" name="nik_pengguna"
                                                value="{{ old('nik_pengguna', Auth::user()->nik_pengguna) }}" required>
                                            @error('nik_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Upload NIK</label>
                                            <div class="text-center mb-3">
                                                <img id="nik-preview" src="{{ Auth::user()->upload_nik_pengguna ? asset('storage/' . Auth::user()->upload_nik_pengguna) : asset('user.png') }}" class="img-fluid"
                                                    style="max-height: 200px;" alt="NIK Preview">
                                            </div>
                                            <input type="file"
                                                class="form-control @error('upload_nik_pengguna') is-invalid @enderror"
                                                id="upload_nik_pengguna" name="upload_nik_pengguna" accept="image/*">
                                            <small class="form-text text-muted">
                                                Format file: jpg, jpeg, png, gif. Maksimal 10MB
                                            </small>
                                            @error('upload_nik_pengguna')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group text-right mt-4">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Ubah Password tab content remains the same -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Preview image before upload
            $('#upload_nik_pengguna').change(function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#nik-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        $(document).ready(function () {
            $('.toggle-password').click(function () {
                let input = $($(this).attr("toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            @if(session('success'))
                alert('{{ session('success') }}');
            @endif

            @if(session('error_type') === 'current_password')
                alert('Password lama tidak sesuai');
            @endif

            @if(session('error_type') === 'new_password' || session('error_type') === 'new_password_confirmation')
                $('#password-tab').tab('show'); // Aktifkan tab "Ubah Password"
            @endif
        });
    </script>

    <style>
        .border-container {
            border: 1px solid black;
            border-radius: 0 10px 10px 10px;
            padding: 20px;
        }

        .nav-menu_profil .nav-link {
            border-radius: 10px 10px 0 0;
            border: 1px solid grey;
            color: black;
            background-color: white;
        }

        .nav-menu_profil .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .form-group input {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }

        .jarak {
            margin-top: 20px;
        }

        .jarak-menu {
            margin-top: 80px;
        }
    </style>
@endsection