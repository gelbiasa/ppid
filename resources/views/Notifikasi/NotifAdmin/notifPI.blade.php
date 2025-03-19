@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <a href="{{ url('Notifikasi/NotifAdmin') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
        <h3 class="card-title"><strong> Notifikasi Pengajuan Permohonan Informasi </strong></h3>
    </div>
    <div class="card-body">
        @if($notifikasi->isEmpty())
            <!-- Jika tidak ada notifikasi -->
            <div class="d-flex flex-column align-items-center justify-content-center"
                style="height: 200px; background-color: #fff3cd; border: 1px solid #856404; border-radius: 10px;">
                <span style="font-size: 50px;">📭</span>
                <p style="margin: 0; font-weight: bold; font-size: 18px; text-align: center;">Tidak ada Notifikasi
                    Permohonan</p>
            </div>
        @else
            <!-- Container Notifikasi -->
            @foreach($notifikasi as $item)
                <div class="p-3 mb-3 notifikasi-item {{ $item->sudah_dibaca ? 'notifikasi-dibaca' : '' }}"
                    style="border-radius: 10px; display: flex; align-items: center; background-color: {{ $item->sudah_dibaca ? '#d4edda' : '#b3e5fc' }};">
                    <i class="fas fa-bell fa-2x" style="margin-right: 15px;"></i>
                    <div style="flex: 1;">
                        <p style="margin: 0; font-weight: bold;">{{ $item->pesan_notif_admin }}</p>
                        <p style="margin: 0;">Status pemohon: {{ $item->t_permohonan_informasi->pi_kategori_pemohon ?? 'Data Sudah Dihapus' }}
                        </p>
                        <p style="margin: 0;">Kategori Aduan: {{ $item->t_permohonan_informasi->pi_kategori_aduan ?? 'Data Sudah Dihapus' }}</p>
                        <p style="margin: 0;">
                            {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                        </p>
                    </div>

                    <!-- Tombol -->
                    <div style="display: flex; flex-direction: column; align-items: flex-end;">
                        <!-- Tombol Hapus -->
                        <button class="btn btn-danger btn-sm hapus-notifikasi" data-id="{{ $item->notif_admin_id }}"
                            data-sudah-dibaca="{{ $item->sudah_dibaca_notif_admin }}" style="width: 132px;">
                            Hapus
                        </button>
                        <!-- Tombol Tandai Telah Dibaca -->
                        @if(!$item->sudah_dibaca_notif_admin)
                            <button class="btn btn-secondary btn-sm mt-2 tandai-dibaca" data-id="{{ $item->notif_admin_id }}">
                                Tandai telah Dibaca
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Tombol Aksi Massal -->
            <div class=>
                <button id="tandai-semua-dibaca" class="btn btn-secondary">
                    <i class="fas fa-check-circle"></i> Tandai Semua Telah Dibaca
                </button>
                <button id="hapus-semua-dibaca" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus Semua Notifikasi
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    // Tandai Telah Dibaca
    document.querySelectorAll('.tandai-dibaca').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;

            // Menampilkan popup SweetAlert2 untuk konfirmasi
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menandai notifikasi ini sebagai telah dibaca.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user mengkonfirmasi, lakukan aksi "tandai telah dibaca"
                    fetch(`{{ url('Notifikasi/NotifAdmin/tandai-dibaca') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false, // Mencegah menutup modal dengan klik luar
                                }).then(() => {
                                    location.reload(); // Reload setelah pengguna menekan OK
                                });
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Terjadi kesalahan!',
                                'Tidak dapat menandai notifikasi.',
                                'error'
                            );
                        });
                }
            });
        });
    });

    // Hapus Notifikasi
    document.querySelectorAll('.hapus-notifikasi').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const sudahDibaca = this.dataset.sudahDibaca; // Ambil status sudah_dibaca

            // Jika notifikasi belum dibaca, tampilkan pesan pemberitahuan
            if (!sudahDibaca || sudahDibaca === 'null') {
                Swal.fire(
                    'Tidak Bisa Dihapus!',
                    'Notifikasi ini tidak bisa dihapus. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.',
                    'warning'
                );
                return; // Keluar dari handler jika belum dibaca
            }

            // Menampilkan popup SweetAlert2 untuk konfirmasi
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Notifikasi ini akan dihapus dan tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user mengkonfirmasi, lakukan aksi "hapus notifikasi"
                    fetch(`{{ url('Notifikasi/NotifAdmin/hapus') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Dihapus!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false, // Mencegah menutup modal dengan klik luar
                                }).then(() => {
                                    location.reload(); // Reload setelah pengguna menekan OK
                                });
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Terjadi kesalahan!',
                                'Tidak dapat menghapus notifikasi.',
                                'error'
                            );
                        });
                }
            });
        });
    });

    // Tandai Semua Telah Dibaca
    document.getElementById('tandai-semua-dibaca').addEventListener('click', function() {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menandai semua notifikasi telah dibaca?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tandai Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('Notifikasi/NotifAdmin/tandai-semua-dibaca') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            data.message,
                            'error'
                        );
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Terjadi kesalahan!',
                        'Tidak dapat menandai semua notifikasi.',
                        'error'
                    );
                });
            }
        });
    });

    // Hapus Semua Notifikasi Telah Dibaca
    document.getElementById('hapus-semua-dibaca').addEventListener('click', function() {
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menghapus semua notifikasi yang telah dibaca?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('Notifikasi/NotifAdmin/hapus-semua-dibaca') }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            data.message,
                            'error'
                        );
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Terjadi kesalahan!',
                        'Tidak dapat menghapus notifikasi.',
                        'error'
                    );
                });
            }
        });
    });

</script>

@endsection