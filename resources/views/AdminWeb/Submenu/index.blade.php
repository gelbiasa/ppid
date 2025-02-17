@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/adminweb/submenu/create_ajax') }}')"
                    class="btn btn-sm btn-success mt-1">Tambah Sub Menu</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm" id="table_submenu">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Menu Utama</th>
                            <th>Nama Sub Menu</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate rows here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        $(document).ready(function() {
            dataSubmenu = $('#table_submenu').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('/adminweb/submenu/list') }}",
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
                        data: "parent_menu",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "wm_menu_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "wm_menu_url",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "wm_status_menu",
                        className: "text-center",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data === 'aktif') {
                                return '<span class="badge bg-success">Aktif</span>';
                            } else if (data === 'nonaktif') {
                                return '<span class="badge bg-danger">Nonaktif</span>';
                            }
                            return '<span class="badge bg-secondary">Unknown</span>';
                        }
                    },
                    {
                        data: "created_at",
                        className: "text-center",
                        orderable: true,
                        searchable: false
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