@extends('layouts.template')

@section('content')

<!-- Flash Message -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Prosedur Pengajuan Permohonan Informasi</h3>
    </div>
    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        <p>Ikuti langkah-langkah berikut untuk mengajukan permohonan:</p>
        <ol style="margin-bottom: 0;">
            <li>Isi Form Pengajuan Permohonan</li>
            <li>Kirim Pengajuan Permohonan</li>
            <li>Tunggu Proses Pengajuan Permohonan</li>
            <li>Dapatkan Hasil Pengajuan Permohonan</li>
        </ol>
    </div>
    <div class="card-body" style="padding-top: 10px;">
        <hr class="thick-line">
        <h4><strong>Permohonan informasi terdiri atas Perorangan dan Organisasi</strong></h4>
        <hr class="thick-line">
        <div class="row text-center">
            <div class="col-md-4">
                <a href="{{ url('SistemInformasi/EForm/RPN/PermohonanInformasi/formPermohonanInformasi') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-edit fa-2x"></i>
                    <h5>E-Form Permohonan Informasi</h5>
                </a>
                <div class="custom-container p-3">
                    <p>Silakan Mengklik Button Diatas Untuk Melakukan Pengisian Form Permohonan Informasi</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .thick-line {
        border: none;
        height: 1px; 
        background-color: black;
    }

    .custom-button {
        background-color: #a59c9c;
        border: 2px solid black;
        border-radius: 8px; 
        color: black; 
        text-decoration: none; 
        transition: background-color 0.3s, transform 0.3s; 
    }

    .custom-button:hover {
        background-color: #8e8585; 
        transform: scale(0.95);
        color: white; /* Warna ikon saat hover */
    }

    .custom-container {
        background-color: #ffffff; 
        border: 2px solid black;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }
</style>

@endsection
