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
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
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

@push('css')
   
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        $(document).ready(function() {
            // Initialize DataTable
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
                        data: "kategori_footer.kt_footer_nama",
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
                                `<img src="{{ asset('storage/') }}/${data}" class="footer-icon" alt="Icon">` : 
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

        // Function to handle delete
        function deleteFooter(id) {
            if (confirm('Apakah Anda yakin ingin menghapus footer ini?')) {
                $.ajax({
                    url: `{{ url('adminweb/footer') }}/${id}/delete`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            footerTable.ajax.reload();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Gagal menghapus footer');
                    }
                });
            }
        }
    </script>
@endpush