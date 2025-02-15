<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="../../index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="logout" id="logout-link" href="#" role="button">
            <i class="fas fa-sign-out-alt" style="color: red;"></i>
        </a>
      </li>
    </ul>
  </nav>

  <script>
    document.getElementById('logout-link').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        // Trigger SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Apakah yakin ingin keluar?',
            text: "Session anda akan berakhir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Log Out',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, redirect to the logout URL
                window.location.href = "{{ url('logout/') }}";
            }
        });
    });
</script>