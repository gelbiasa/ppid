<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="card">
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">Nama Submenu Pengumuman</th>
            <td>{{ $pengumumanDinamis->pd_nama_submenu }}</td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($pengumumanDinamis->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $pengumumanDinamis->created_by }}</td>
          </tr>
          @if($pengumumanDinamis->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($pengumumanDinamis->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $pengumumanDinamis->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
    
    <div class="alert alert-info mt-3">
      <i class="fas fa-info-circle mr-2"></i> <strong>Informasi:</strong> 
      Submenu pengumuman ini akan muncul pada menu Pengumuman di website publik. Anda dapat menambahkan konten pengumuman sesuai dengan submenu ini melalui menu pengelolaan konten.
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>