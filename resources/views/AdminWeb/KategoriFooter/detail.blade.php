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
            <th width="200">Kode Kategori Footer</th>
            <td>{{ $kategoriFooter->kt_footer_kode }}</td>
          </tr>
          <tr>
            <th>Nama Kategori Footer</th>
            <td>{{ $kategoriFooter->kt_footer_nama }}</td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($kategoriFooter->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $kategoriFooter->created_by }}</td>
          </tr>
          @if($kategoriFooter->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($kategoriFooter->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $kategoriFooter->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>