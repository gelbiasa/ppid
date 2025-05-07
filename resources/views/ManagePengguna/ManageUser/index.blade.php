@php
  use App\Models\Website\WebMenuModel;
  use App\Models\HakAkses\SetHakAksesModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h3 class="card-title">{{ $page->title }}</h3>
        </div>
        <div class="col-md-6 text-right">
          <div class="card-tools">
            @if(
              Auth::user()->level->hak_akses_kode === 'SAR' ||
              SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $managementUserUrl, 'create')
            )
              <button onclick="modalAction('{{ url($managementUserUrl . '/addData') . (isset($levelId) ? '?level_id='.$levelId : '') }}')" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Tambah
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <!-- Tab untuk memilih level -->
      <ul class="nav nav-tabs" id="levelTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link {{ empty($levelId) ? 'active' : '' }}" id="all-tab" href="{{ url($managementUserUrl) }}">
            Semua Pengguna
          </a>
        </li>
        @foreach($hakAkses as $hak)
          <li class="nav-item">
            <a class="nav-link {{ $levelId == $hak->hak_akses_id ? 'active' : '' }}" 
               id="level-{{ $hak->hak_akses_id }}-tab" 
               href="{{ url($managementUserUrl) }}?level_id={{ $hak->hak_akses_id }}">
              {{ $hak->hak_akses_nama }}
            </a>
          </li>
        @endforeach
      </ul>

      <div class="tab-content mt-3" id="levelTabContent">
        <div class="tab-pane fade show active" id="level-content" role="tabpanel">
          <div class="row mb-3">
            <div class="col-md-6">
              <form id="searchForm" class="d-flex">
                @if(!empty($levelId))
                  <input type="hidden" name="level_id" value="{{ $levelId }}">
                @endif
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, NIK, atau no HP"
                  value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary ml-2">
                  <i class="fas fa-search"></i>
                </button>
              </form>
            </div>
          </div>

          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif

          @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <div class="table-responsive" id="table-container">
            @include('ManagePengguna/ManageUser.data')
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

@push('css')
  <style>
    .pagination {
      justify-content: flex-start;
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
      // URL dinamis untuk Management User
      var managementUserUrl = '{{ $managementUserUrl }}';

      // Handle search form submission
      $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        var search = $(this).find('input[name="search"]').val();
        var levelId = $(this).find('input[name="level_id"]').val();
        loadUserData(1, search, levelId);
      });

      // Handle pagination links with delegation
      $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var search = $('#searchForm input[name="search"]').val();
        var levelId = $('#searchForm input[name="level_id"]').val();
        loadUserData(page, search, levelId);
      });

      // Fungsi untuk memuat data user
      function loadUserData(page, search, levelId) {
        $.ajax({
          url: '{{ url('') }}/' + managementUserUrl + '/getData',
          type: 'GET',
          data: {
            page: page,
            search: search,
            level_id: levelId
          },
          success: function (response) {
            $('#table-container').html(response);
          },
          error: function (xhr) {
            alert('Terjadi kesalahan saat memuat data');
          }
        });
      }

      // Fungsi modalAction yang akan digunakan untuk menampilkan modal
      function modalAction(action) {
        $('#myModal .modal-content').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
        $('#myModal').modal('show');

        $.ajax({
          url: action,
          type: 'GET',
          success: function (response) {
            $('#myModal .modal-content').html(response);
          },
          error: function (xhr) {
            console.error('Ajax Error:', xhr);
            var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Terjadi kesalahan: ' + xhr.status + ' ' + xhr.statusText;
            
            $('#myModal .modal-content').html(`
              <div class="modal-header">
                <h5 class="modal-title">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="alert alert-danger">
                  ${errorMessage}
                </div>
              </div>
            `);
          }
        });
      }

      // Fungsi untuk me-reload tabel
      function reloadTable() {
        var currentPage = $('.pagination .active .page-link').text();
        currentPage = currentPage || 1;
        var search = $('#searchForm input[name="search"]').val();
        var levelId = $('#searchForm input[name="level_id"]').val();
        loadUserData(currentPage, search, levelId);
      }

      // Expose fungsi-fungsi ke lingkup global agar bisa dipanggil dari luar
      window.modalAction = modalAction;
      window.reloadTable = reloadTable;
    });
  </script>
@endpush