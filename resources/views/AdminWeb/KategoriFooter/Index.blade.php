@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/adminweb/kategori-footer/create') }}')" class="btn btn-sm btn-success mt-1">
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
        // Global variable untuk DataTable
        var kategoriFooterTable;

        // Fungsi untuk membuka modal
        function modalAction(url = '') {
            $('#myModal').load(url, function(response) {
                $('#myModal').modal('show');
            }).fail(function() {
                toastr.error('Gagal memuat konten modal');
            });
        }

        // Fungsi untuk menghapus kategori footer
        function deleteKategoriFooter(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus kategori footer ini?',
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
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );
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
                columns: [
                    {
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