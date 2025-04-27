@extends('layouts.template')

@section('content')
<div class="error-page">
    <h2 class="headline text-warning">403</h2>
    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Akses Ditolak!</h3>
        <p>
            {{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>
        <p>
            <a href="{{ url()->previous() }}" class="btn btn-warning">Kembali</a>
            @php
                // Tentukan URL dashboard berdasarkan level pengguna
                $dashboardUrl = '/';
                if (Auth::check()) {
                    $hakAksesKode = Auth::user()->level->hak_akses_kode;
                    $dashboardUrl = '/dashboard' . $hakAksesKode;
                }
            @endphp
            <a href="{{ url($dashboardUrl) }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </p>
    </div>
</div>
@endsection