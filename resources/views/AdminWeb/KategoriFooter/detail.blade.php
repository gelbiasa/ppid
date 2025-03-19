<!-- views/AdminWeb/KategoriFooter/detail.blade.php -->

<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">Detail Kategori Footer</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
</div>
 
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Kode Footer</th>
                    <td>{{ $kategoriFooter->kt_footer_kode }}</td>
                </tr>
                <tr>
                    <th>Nama Footer</th>
                    <td>{{ $kategoriFooter->kt_footer_nama }}</td>
                </tr>
                <tr>
                    <th>Dibuat Oleh</th>
                    <td>{{ $kategoriFooter->created_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $kategoriFooter->created_at ? date('d-m-Y H:i:s', strtotime($kategoriFooter->created_at)) : '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Oleh</th>
                    <td>{{ $kategoriFooter->updated_by ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>{{ $kategoriFooter->updated_at ? date('d-m-Y H:i:s', strtotime($kategoriFooter->updated_at)) : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
 
<div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
     <button type="button" class="btn btn-warning" onclick="modalAction('{{ url('adminweb/kategori-footer/editData/' . $kategoriFooter->kategori_footer_id) }}')">Edit</button>
</div>