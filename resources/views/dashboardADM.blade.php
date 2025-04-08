@extends('layouts.template')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Halo Pengguna</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            Selamat Datang Admin di PPID Polinema

            <div class="card-footer">
                <p>Nama Pengguna: {{ session('nama_pengguna') }}</p>
                <p>Email: {{ session('email_pengguna') }}</p>
                <p>Alamat: {{ session('alamat_pengguna')}}
                <p>No HP: {{ session('no_hp_pengguna') }}</p>
                <p>Pekerjaan: {{ session('pekerjaan_pengguna') }}</p>
                <p>NIK: {{ session('nik_pengguna')}}
                <p>Foto NIk: {{ session('upload_nik_pengguna')}}
                <p>Alias: {{ session('alias') }}</p>
            </div>

            <!-- TAMBAHAN: Debug session untuk memverifikasi data -->
            @if(app()->environment('local'))
            <hr>
            <h4>Debug Session Data:</h4>
            <pre>{{ print_r(session()->all(), true) }}</pre>
            @endif

        </div>
    </div>

@endsection