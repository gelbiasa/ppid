@extends('layouts.template')

@section('content')

<?php

use App\Models\Log\NotifAdminModel;

// Hitung jumlah notifikasi untuk kategori 'permohonan'
$jumlahNotifikasiPermohonan = NotifAdminModel::where('kategori_notif_admin', 'E-Form Permohonan Informasi')
    ->whereNull('sudah_dibaca_notif_admin')
    ->where('isDeleted', 0)
    ->count();
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Notifikasi Pengajuan Permohonan dan Pertanyaan</h3>
    </div>
    <div class="card-body" style="padding-top: 10px;">
        <div class="row text-center">
            <!-- Permohonan -->
            <div class="col-md-6">
                <a href="{{ url('Notifikasi/NotifAdmin/notifPI') }}" class="custom-button d-block p-3 mb-2 position-relative">
                    <i class="fas fa-file-alt fa-2x"></i>
                    <h5>Pengajuan Permohonan Informasi</h5>
                    @if($jumlahNotifikasiPermohonan > 0)
                        <span class="badge badge-danger notification-badge-menu">{{ $jumlahNotifikasiPermohonan }}</span>
                    @endif
                </a>
            </div>            
        </div>        
    </div>
</div>

<style>
    .custom-button {
        background-color: lightblue;
        border: 2px solid black;
        border-radius: 8px; 
        color: black; 
        text-decoration: none; 
        transition: background-color 0.3s, transform 0.3s; 
        position: relative; /* Untuk badge absolut dalam elemen ini */
    }

    .custom-button:hover {
        background-color: blue; 
        transform: scale(0.95);
        color: white; /* Warna ikon saat hover */
    }

    .notification-badge-menu {
        position: absolute;
        top: 5px; /* Atur posisi vertikal */
        right: 5px; /* Atur posisi horizontal */
        background-color: #dc3545; /* Warna merah */
        color: white; /* Warna teks */
        padding: 3px 8px; /* Spasi dalam */
        border-radius: 50%; /* Membulatkan badge */
        font-size: 20px; /* Ukuran font */
        font-weight: bold; /* Tebal */
    }
</style>

@endsection