<!-- views/AdminWeb/AksesCepat/detail.blade.php -->

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
                    <th width="200">Kategori Akses Cepat</th>
                    <td>{{ $aksesCepat->kategoriAkses ? $aksesCepat->kategoriAkses->mka_judul_kategori : '-' }}</td>
                </tr>
                <tr>
                    <th>Judul Akses Cepat</th>
                    <td>{{ $aksesCepat->ac_judul }}</td>
                </tr>
                <tr>
                    <th>URL Akses Cepat</th>
                    <td>
                        @if($aksesCepat->ac_url)
                            <a href="{{ $aksesCepat->ac_url }}" target="_blank">{{ $aksesCepat->ac_url }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Icon Statis</th>
                    <td>
                        @if($aksesCepat->ac_static_icon)
                            <img src="{{ asset('storage/akses_cepat_static_icons/' . basename($aksesCepat->ac_static_icon)) }}"
                                 alt="Icon Statis"
                                 style="max-width: 100px; max-height: 100px;">
                            <br>
                            <small>{{ basename($aksesCepat->ac_static_icon) }}</small>
                        @else
                            Tidak ada ikon
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Icon Animasi</th>
                    <td>
                        @if($aksesCepat->ac_animation_icon)
                            <img src="{{ asset('storage/akses_cepat_animation_icons/' . basename($aksesCepat->ac_animation_icon)) }}"
                                 alt="Icon Animasi"
                                 style="max-width: 100px; max-height: 100px;">
                            <br>
                            <small>{{ basename($aksesCepat->ac_animation_icon) }}</small>
                        @else
                            Tidak ada ikon
                        @endif
                    </td>
                </tr>
                
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $aksesCepat->created_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $aksesCepat->created_at ? date('d-m-Y H:i:s', strtotime($aksesCepat->created_at)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $aksesCepat->updated_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>{{ $aksesCepat->updated_at ? date('d-m-Y H:i:s', strtotime($aksesCepat->updated_at)) : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
 
<div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>