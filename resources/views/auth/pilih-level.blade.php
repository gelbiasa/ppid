<!-- resources/views/auth/pilih-level.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pilih Level</title>

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
        <h1 class="h4">Pilih Level Akses</h1>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Selamat datang, <strong>{{ $user->nama_pengguna }}</strong>. Silakan pilih level akses untuk masuk ke sistem.</p>

        <form action="{{ route('pilih.level.post') }}" method="POST">
          @csrf
          <div class="form-group">
            @foreach($hakAkses as $level)
            <div class="custom-control custom-radio">
              <input class="custom-control-input" type="radio" id="hakAkses{{ $level->hak_akses_id }}" name="hak_akses_id" value="{{ $level->hak_akses_id }}">
              <label for="hakAkses{{ $level->hak_akses_id }}" class="custom-control-label">
                <strong>{{ $level->hak_akses_nama }}</strong> ({{ $level->hak_akses_kode }})
              </label>
            </div>
            @endforeach
          </div>

          @if(session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
          @endif

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </div>
          </div>
        </form>

        <div class="mt-3 text-center">
          <a href="{{ url('logout') }}" class="text-danger">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

  <!-- Bootstrap 4 -->
  <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- AdminLTE App -->
  <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

  <script>
    $(document).ready(function() {
      // Pilih otomatis opsi pertama
      $('input[name="hak_akses_id"]:first').prop('checked', true);
    });
  </script>
</body>

</html>