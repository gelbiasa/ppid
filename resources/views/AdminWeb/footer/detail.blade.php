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
            <th width="200">Kategori Footer</th>
            <td>{{ $footer->kategoriFooter->kt_footer_nama ?? 'Tidak Ada' }}</td>
          </tr>
          <tr>
            <th>Judul Footer</th>
            <td>{{ $footer->f_judul_footer }}</td>
          </tr>
          <tr>
            <th>URL Footer</th>
            <td>
                @if($footer->f_url_footer)
                    <a href="{{ $footer->f_url_footer }}" target="_blank">{{ $footer->f_url_footer }}</a>
                @else
                    -
                @endif
            </td>
          </tr>
          <tr>
            <th>Ikon Footer</th>
            <td>
              @if($footer->f_icon_footer)
              <img src="{{ asset('storage/footer_icons/' . basename($footer->f_icon_footer)) }}" 
                   alt="{{ $footer->f_judul_footer }}" 
                   style="max-width: 100px; max-height: 100px;">
              <br>
              <small>{{ basename($footer->f_icon_footer) }}</small>
          @else
              Tidak ada ikon
          @endif
            </td>
          </tr>
          <tr>
            <th>Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($footer->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $footer->created_by }}</td>
          </tr>
          @if($footer->updated_by)
          <tr>
            <th>Terakhir Diperbarui</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($footer->updated_at)) }}</td>
          </tr>
          <tr>
            <th>Diperbarui Oleh</th>
            <td>{{ $footer->updated_by }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>