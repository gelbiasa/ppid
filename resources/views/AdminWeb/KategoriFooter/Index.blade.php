@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/adminweb/kategori-footer/create') }}')"
                    class="btn btn-sm btn-success mt-1">
                    <i class="fas fa-plus"></i> Tambah Kategori Footer
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori_footer">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        //  variable untuk DataTable
        var kategoriFooterTable;

        // Fungsi untuk membuka modal
        function modalAction(url = '') {
            $('#myModal').load(url, function(response) {
                $('#myModal').modal('show');
            }).fail(function() {
                toastr.error('Gagal memuat konten modal');
            });
        }
        // lihat detail kategori
        function showDetailKategoriFooter(id) {
            $.ajax({
                url: `{{ url('/adminweb/kategori-footer') }}/${id}/detail_kategoriFooter`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#myModal').html(`
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Kategori Footer</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 40%">Nama Kategori</th>
                                        <td>${response.kategori_footer.kt_footer_nama}</td>
                                    </tr>
                                    <tr>
                                        <th>Kode Kategori</th>
                                        <td>${response.kategori_footer.kt_footer_kode}</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Oleh</th>
                                        <td>${response.kategori_footer.created_by}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Dibuat</th>
                                        <td>${response.kategori_footer.created_at}</td>
                                    </tr>
                                    <tr>
                                        <th>Diperbarui Oleh</th>
                                        <td>${response.kategori_footer.updated_by || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Diperbarui</th>
                                        <td>${response.kategori_footer.updated_at || '-'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                `);
                        $('#myModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail kategori footer', 'error');
                }
            });
        }

        // Fungsi untuk menghapus kategori footer
        function deleteKategoriFooter(id) {

            $.ajax({
                url: `{{ url('/adminweb/kategori-footer') }}/${id}/detail_kategoriFooter`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const namaKategori = response.kategori_footer.kt_footer_nama;

                        // Tampilkan konfirmasi dengan nama kategori 
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus kategori footer <strong>"${namaKategori}"</strong>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `{{ url('adminweb/kategori-footer') }}/${id}/delete`,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            Swal.fire({
                                                title: 'Terhapus!',
                                                html: `Kategori <strong>"${namaKategori}"</strong> berhasil dihapus.`,
                                                icon: 'success'
                                            });
                                            kategoriFooterTable.ajax.reload();
                                        } else {
                                            Swal.fire(
                                                'Gagal!',
                                                response.message,
                                                'error'
                                            );
                                        }
                                    },
                                    error: function(xhr) {
                                        Swal.fire(
                                            'Error!',
                                            'Gagal menghapus kategori footer',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal mengambil detail kategori footer', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail kategori footer', 'error');
                }
            });
        }


        // Inisialisasi DataTable
        $(document).ready(function() {
            kategoriFooterTable = $('#table_kategori_footer').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('adminweb/kategori-footer/list') }}",
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "kt_footer_kode",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "kt_footer_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
