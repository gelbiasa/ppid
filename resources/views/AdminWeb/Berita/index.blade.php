@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="card-title">{{ $page->title }}</h3>
            </div>
            <div class="col-md-6 text-right">
                <button onclick="showTypeModal()" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Tambah Berita
                </button>   
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form id="searchForm" class="d-flex">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari judul berita" 
                           value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="table-responsive" id="table-container">
            @include('AdminWeb.Berita.data')
        </div>
    </div>
</div>

<!-- Modal Pilih Tipe Berita -->
<div class="modal fade" id="typeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tipe Berita</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-md-6">
                        <button onclick="redirectToAdd('file')" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-file-alt mr-2"></i>Berita File
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button onclick="redirectToAdd('link')" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-link mr-2"></i>Berita Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for CRUD operations -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function showTypeModal() {
        $('#typeModal').modal('show');
    }

    function redirectToAdd(type) {
        $('#typeModal').modal('hide');
        modalAction('{{ url("adminweb/upload-berita/addData") }}?type=' + type);
    }

    @push('js')
<script>
    // Fungsi modalAction
    function modalAction(url) {
        $('#myModal .modal-content').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
        $('#myModal').modal('show');
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#myModal .modal-content').html(response);
            },
            error: function(xhr) {
                $('#myModal .modal-content').html('<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div></div>');
            }
        });
    }

    function showTypeModal() {
        $('#typeModal').modal('show');
    }

    function redirectToAdd(type) {
        $('#typeModal').modal('hide');
        modalAction('{{ url("adminweb/berita/addData") }}?type=' + type);
    }

    function reloadTable() {
        var currentPage = $('.pagination .active .page-link').text();
        currentPage = currentPage || 1;
        var search = $('#searchForm input[name="search"]').val();
        loadBeritaData(currentPage, search);
    }

    function loadBeritaData(page, search) {
        $.ajax({
            url: '{{ url("adminweb/berita/getData") }}',
            type: 'GET',
            data: {
                page: page,
                search: search
            },
            success: function(response) {
                $('#table-container').html(response);
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    }

    $(document).ready(function() {
        // Handle search form submission
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var search = $(this).find('input[name="search"]').val();
            loadBeritaData(1, search);
        });

        // Handle pagination links with delegation
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var search = $('#searchForm input[name="search"]').val();
            loadBeritaData(page, search);
        });
    });
</script>
@endpush
</script>
@endpush