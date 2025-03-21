@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
          <button onclick="modalAction('{{ url('AdminWeb/Pengumuman/addData') }}')" class="btn btn-sm btn-success mt-1">
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
          <table class="table table-bordered table-striped table-hover table-sm" id="table_pengumuman">
              <thead>
                  <tr>
                      <th width="5%">No</th>
                      <th width="20%">Kategori Pengumuman</th>
                      <th width="25%">Judul</th>
                      <th width="15%">Tipe</th>
                      <th width="10%">Status</th>
                      <th width="25%">Aksi</th>
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
<style>
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-aktif {
        background-color: #28a745;
        color: white;
    }
    .status-tidak-aktif {
        background-color: #dc3545;
        color: white;
    }
</style>
@endpush

@push('js')
  <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#table_pengumuman').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
          url: '{{ url("AdminWeb/Pengumuman/getData") }}',
          type: 'GET',
        },
        columns: [
          { data: 0 }, // No
          { data: 1 }, // Kategori Pengumuman
          { data: 2 }, // Judul
          { 
            data: 3,
            render: function(data) {
              if (data === 'link') {
                return '<span class="badge badge-info">Link</span>';
              } else if (data === 'file') {
                return '<span class="badge badge-primary">File</span>';
              } else if (data === 'konten') {
                return '<span class="badge badge-success">Konten</span>';
              } else {
                return data;
              }
            }
          }, // Tipe
          { 
            data: 4,
            render: function(data) {
              if (data === 'aktif') {
                return '<span class="status-badge status-aktif">Aktif</span>';
              } else {
                return '<span class="status-badge status-tidak-aktif">Tidak Aktif</span>';
              }
            }
          }, // Status
          { data: 5, orderable: false }, // Aksi
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