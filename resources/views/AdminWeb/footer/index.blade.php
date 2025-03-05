@extends('layouts.template')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/adminweb/footer/create') }}')" class="btn btn-sm btn-success mt-1">
                    <i class="fas fa-plus"></i> Tambah Footer
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="kategori-filter" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriFooters as $kategori)
                            <option value="{{ $kategori->kategori_footer_id }}">
                                {{ $kategori->kt_footer_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table table-bordered table-striped table-hover table-sm" id="table_footer">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Judul</th>
                        <th>URL</th>
                        <th>Ikon</th>
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
        var footerTable;

        // Fungsi untuk membuka modal
        function modalAction(url = '') {
            $('#myModal').load(url, function(response) {
                $('#myModal').modal('show');
            }).fail(function() {
                toastr.error('Gagal memuat konten modal');
            });
        }
        
        // Lihat detail footer
        function showDetailFooter(id) {
            $.ajax({
                url: `{{ url('/adminweb/footer') }}/${id}/detail_footer`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#myModal').html(`
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Footer</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 40%">Judul</th>
                                                <td>${response.footer.f_judul_footer}</td>
                                            </tr>
                                            <tr>
                                                <th>Kategori</th>
                                                <td>${response.footer.kategori_footer}</td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td>${response.footer.f_url_footer ? `<a href="${response.footer.f_url_footer}" target="_blank">${response.footer.f_url_footer}</a>` : '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Ikon</th>
                                                <td>${response.footer.f_icon_footer ? `<img src="{{ asset('storage/') }}/${response.footer.f_icon_footer}" class="img-fluid" style="max-height: 100px;" alt="Icon">` : '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Oleh</th>
                                                <td>${response.footer.created_by}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Dibuat</th>
                                                <td>${response.footer.created_at}</td>
                                            </tr>
                                            <tr>
                                                <th>Diperbarui Oleh</th>
                                                <td>${response.footer.updated_by || '-'}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Diperbarui</th>
                                                <td>${response.footer.updated_at || '-'}</td>
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
                    Swal.fire('Error', 'Gagal mengambil detail footer', 'error');
                }
            });
        }

        // Fungsi untuk menghapus footer
        function deleteFooter(id) {
            $.ajax({
                url: `{{ url('/adminweb/footer') }}/${id}/detail_footer`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const judulFooter = response.footer.f_judul_footer;
                        
                        // Tampilkan konfirmasi dengan judul footer bold
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus footer <strong>"${judulFooter}"</strong>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `{{ url('adminweb/footer') }}/${id}/delete`,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            Swal.fire({
                                                title: 'Terhapus!',
                                                html: `Footer <strong>"${judulFooter}"</strong> berhasil dihapus.`,
                                                icon: 'success'
                                            });
                                            footerTable.ajax.reload();
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
                                            'Gagal menghapus footer',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal mengambil detail footer', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail footer', 'error');
                }
            });
        }

        // Inisialisasi DataTable
        $(document).ready(function() {
            footerTable = $('#table_footer').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('adminweb/footer/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.kategori = $('#kategori-filter').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "kategori_footer",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "f_judul_footer",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "f_url_footer",
                        className: "",
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return data ? 
                                `<a href="${data}" target="_blank">${data}</a>` : 
                                '-';
                        }
                    },
                    {
                        data: "f_icon_footer",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data ? 
                                `<img src="{{ asset('storage/') }}/${data}" class="img-thumbnail" style="max-height: 50px;" alt="Icon">` : 
                                '-';
                        }
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Kategori filter change event
            $('#kategori-filter').on('change', function() {
                footerTable.ajax.reload();
            });
        });
    </script>
@endpush