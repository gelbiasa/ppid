@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
          <button onclick="modalAction('{{ url('SistemInformasi/KategoriForm/addData') }}')" class="btn btn-sm btn-success mt-1">
            <i class="fas fa-plus"></i> Tambah
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

        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori_form">
              <thead>
                  <tr>
                      <th width="5%">Nomor</th>
                      <th width="65%">Nama Kategori Form</th>
                      <th width="30%">Aksi</th>
                  </tr>
              </thead>
              <tbody>
                <!-- Data will be loaded by DataTables -->
              </tbody>
          </table>
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

@push('css')
@endpush

@push('js')
  <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#table_kategori_form').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
          url: '{{ url("SistemInformasi/KategoriForm/getData") }}',
          type: 'GET',
        },
        columns: [
          { data: 0 }, // Nomor
          { data: 1 }, // Nama Kategori Form
          { data: 2, orderable: false }, // Aksi
        ],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
      });
    });
    
    // Function to load modal content
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
  </script>
@endpush