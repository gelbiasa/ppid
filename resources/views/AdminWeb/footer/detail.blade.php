<!-- views/AdminWeb/Footer/detail.blade.php -->

<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">{{ $title }}</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
</div>
 
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Kategori Footer</th>
                    <td>{{ $footer->kategoriFooter ? $footer->kategoriFooter->kt_footer_nama : '-' }}</td>
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
                    <th>Icon Footer</th>
                    <td>
                        @if($footer->f_icon_footer)
                            <img src="{{ asset('storage/' . $footer::ICON_PATH . '/' . $footer->f_icon_footer) }}" 
                                alt="Icon Footer" class="img-thumbnail" style="height: 100px;">
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $footer->created_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $footer->created_at ? date('d-m-Y H:i:s', strtotime($footer->created_at)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $footer->updated_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>{{ $footer->updated_at ? date('d-m-Y H:i:s', strtotime($footer->updated_at)) : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
 
<div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
     <button type="button" class="btn btn-warning" onclick="modalAction('{{ url('adminweb/footer/editData/' . $footer->footer_id) }}')">Edit</button>
</div>