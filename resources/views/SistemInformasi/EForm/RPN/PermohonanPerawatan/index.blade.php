@php
  use App\Models\Website\WebMenuModel;
  $permohonanPerawatanRespondenUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana');
@endphp
@extends('layouts.template')
@section('content')

<?php
// Di bagian awal view atau sebelum dibutuhkan
$data = app()->call([app('App\Http\Controllers\SistemInformasi\EForm\PermohonanPerawatanController'), 'getData']);
$timeline = $data['timeline'];
$ketentuanPelaporan = $data['ketentuanPelaporan'];
?>
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
        <h3 class="card-title">
            @if(isset($timeline))
                {{ $timeline->judul_timeline }}
            @else
                Prosedur Pengajuan Permohonan Perawatan Sarana Prasarana
            @endif
        </h3>
    </div>
    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        <p>Ikuti langkah-langkah berikut untuk mengajukan Permohonan Perawatan Sarana Prasarana:</p>
        <ol style="margin-bottom: 0;">
            @if(isset($timeline) && $timeline->langkahTimeline->count() > 0)
                @foreach($timeline->langkahTimeline as $langkah)
                    <li>{{ $langkah->langkah_timeline }}</li>
                @endforeach
            @else
                <li>Belum ada Timeline</li>
            @endif
        </ol>
    </div>
    
    <!-- Beri jarak dengan margin dan garis pembatas -->
    <div class="mt-4 mb-4">
    </div>
    
    <!-- Header dengan judul di tengah dan bold -->
    <div class="card-header text-center">
        <h3 class="card-title font-weight-bold" style="float: none; display: inline-block;">
            @if(isset($ketentuanPelaporan))
                {{ $ketentuanPelaporan->kp_judul }}
            @else
                Ketentuan Pelaporan Permohonan Perawatan Sarana Prasarana
            @endif
        </h3>
    </div>
    
    <div class="card-body" style="margin-bottom: 0; padding-bottom: 0;">
        @if(isset($ketentuanPelaporan))
            {!! $ketentuanPelaporan->kp_konten !!}
        @else
            <p>Belum ada ketentuan pelaporan yang tersedia.</p>
        @endif
    </div>
    
    <div class="card-body" style="padding-top: 10px;">
        <hr class="thick-line">
        <h4><strong>Silakan laporkan Permohonan Perawatan Sarana Prasarana Anda melalui form berikut</strong></h4>
        <hr class="thick-line">
        <div class="row text-center">
            <div class="col-md-4">
                <a href="{{ url($permohonanPerawatanRespondenUrl . '/addData') }}" class="custom-button d-block p-3 mb-2">
                    <i class="fas fa-edit fa-2x"></i>
                    <h5>E-Form Permohonan Perawatan Sarana Prasarana</h5>
                </a>
                <div class="custom-container p-3">
                    <p>Silakan Mengklik Button Diatas Untuk Melakukan Pengisian Form Permohonan Perawatan Sarana Prasarana</p>
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