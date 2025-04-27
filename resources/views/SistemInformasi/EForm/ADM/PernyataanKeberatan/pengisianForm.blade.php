<!-- pengisian form halaman admin -->
@php
  use App\Models\Website\WebMenuModel;
  $pernyataanKeberatanAdminUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin');
@endphp
@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url($pernyataanKeberatanAdminUrl) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Pernyataan Keberatan </strong></h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url($pernyataanKeberatanAdminUrl . '/createData') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-group">
                    <label for="pk_kategori_pemohon">Pernyataan Keberatan Dilakukan Atas <span class="text-danger">*</span></label>
                    <select class="form-control @error('t_pernyataan_keberatan.pk_kategori_pemohon') is-invalid @enderror" 
                        id="pk_kategori_pemohon" name="t_pernyataan_keberatan[pk_kategori_pemohon]" required>
                        <option value="">-- Silakan Pilih Kategori Pemohon --</option>
                        <option value="Diri Sendiri" {{ old('t_pernyataan_keberatan.pk_kategori_pemohon') == 'Diri Sendiri' ? 'selected' : '' }}>Diri Sendiri</option>
                        <option value="Orang Lain" {{ old('t_pernyataan_keberatan.pk_kategori_pemohon') == 'Orang Lain' ? 'selected' : '' }}>Orang Lain</option>
                    </select>
                    @error('t_pernyataan_keberatan.pk_kategori_pemohon')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form untuk Diri Sendiri Bagian Admin -->
                <div id="formDiriSendiri" style="display: none;">
                    <div class="form-group">
                        <label for="pk_nama_pengguna">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_diri_sendiri.pk_nama_pengguna') is-invalid @enderror" 
                            id="pk_nama_pengguna" name="t_form_pk_diri_sendiri[pk_nama_pengguna]" 
                            value="{{ old('t_form_pk_diri_sendiri.pk_nama_pengguna') }}">
                        @error('t_form_pk_diri_sendiri.pk_nama_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pk_alamat_pengguna">Alamat Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_diri_sendiri.pk_alamat_pengguna') is-invalid @enderror" 
                            id="pk_alamat_pengguna" name="t_form_pk_diri_sendiri[pk_alamat_pengguna]" 
                            value="{{ old('t_form_pk_diri_sendiri.pk_alamat_pengguna') }}">
                        @error('t_form_pk_diri_sendiri.pk_alamat_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pk_pekerjaan_pengguna">Pekerjaan Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_diri_sendiri.pk_pekerjaan_pengguna') is-invalid @enderror" 
                            id="pk_pekerjaan_pengguna" name="t_form_pk_diri_sendiri[pk_pekerjaan_pengguna]" 
                            value="{{ old('t_form_pk_diri_sendiri.pk_pekerjaan_pengguna') }}">
                        @error('t_form_pk_diri_sendiri.pk_pekerjaan_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pk_no_hp_pengguna">No Hp Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_diri_sendiri.pk_no_hp_pengguna') is-invalid @enderror" 
                            id="pk_no_hp_pengguna" name="t_form_pk_diri_sendiri[pk_no_hp_pengguna]" 
                            value="{{ old('t_form_pk_diri_sendiri.pk_no_hp_pengguna') }}">
                        @error('t_form_pk_diri_sendiri.pk_no_hp_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pk_email_pengguna">Email Pelapor <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('t_form_pk_diri_sendiri.pk_email_pengguna') is-invalid @enderror" 
                            id="pk_email_pengguna" name="t_form_pk_diri_sendiri[pk_email_pengguna]" 
                            value="{{ old('t_form_pk_diri_sendiri.pk_email_pengguna') }}">
                        @error('t_form_pk_diri_sendiri.pk_email_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="pk_upload_nik_pengguna">Upload NIK Pelapor <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pk_upload_nik_pengguna') is-invalid @enderror" 
                            id="pk_upload_nik_pengguna" name="pk_upload_nik_pengguna" accept="image/*">
                        @error('pk_upload_nik_pengguna')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form untuk Orang Lain Bagian Admin -->
                <div id="formOrangLain" style="display: none;">
                    <div class="form-group">
                        <label for="pk_nama_pengguna_penginput">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_nama_pengguna_penginput') is-invalid @enderror" 
                            id="pk_nama_pengguna_penginput" name="t_form_pk_orang_lain[pk_nama_pengguna_penginput]" 
                            value="{{ old('t_form_pk_orang_lain.pk_nama_pengguna_penginput') }}">
                        @error('t_form_pk_orang_lain.pk_nama_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_alamat_pengguna_penginput">Alamat Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_alamat_pengguna_penginput') is-invalid @enderror"
                            id="pk_alamat_pengguna_penginput" name="t_form_pk_orang_lain[pk_alamat_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_alamat_pengguna_penginput') }}">
                        @error('t_form_pk_orang_lain.pk_alamat_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_pekerjaan_pengguna_penginput">Pekerjaan Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput') is-invalid @enderror"
                            id="pk_pekerjaan_pengguna_penginput" name="t_form_pk_orang_lain[pk_pekerjaan_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput') }}">
                        @error('t_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_no_hp_pengguna_penginput">No Hp Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_no_hp_pengguna_penginput') is-invalid @enderror" 
                            id="pk_no_hp_pengguna_penginput" name="t_form_pk_orang_lain[pk_no_hp_pengguna_penginput]" 
                            value="{{ old('t_form_pk_orang_lain.pk_no_hp_pengguna_penginput') }}">
                        @error('t_form_pk_orang_lain.pk_no_hp_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_email_pengguna_penginput">Email Pelapor <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('t_form_pk_orang_lain.pk_email_pengguna_penginput') is-invalid @enderror" 
                            id="pk_email_pengguna_penginput" name="t_form_pk_orang_lain[pk_email_pengguna_penginput]" 
                            value="{{ old('t_form_pk_orang_lain.pk_email_pengguna_penginput') }}">
                        @error('t_form_pk_orang_lain.pk_email_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_upload_nik_pengguna_penginput">Upload NIK Pelapor <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pk_upload_nik_pengguna_penginput') is-invalid @enderror" 
                            id="pk_upload_nik_pengguna_penginput" name="pk_upload_nik_pengguna_penginput" accept="image/*">
                        @error('pk_upload_nik_pengguna_penginput')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_nama_kuasa_pemohon">Nama Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_nama_kuasa_pemohon') is-invalid @enderror" 
                            id="pk_nama_kuasa_pemohon" name="t_form_pk_orang_lain[pk_nama_kuasa_pemohon]" 
                            value="{{ old('t_form_pk_orang_lain.pk_nama_kuasa_pemohon') }}">
                        @error('t_form_pk_orang_lain.pk_nama_kuasa_pemohon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_alamat_kuasa_pemohon">Alamat Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_alamat_kuasa_pemohon') is-invalid @enderror" 
                            id="pk_alamat_kuasa_pemohon" name="t_form_pk_orang_lain[pk_alamat_kuasa_pemohon]" 
                            value="{{ old('t_form_pk_orang_lain.pk_alamat_kuasa_pemohon') }}">
                        @error('t_form_pk_orang_lain.pk_alamat_kuasa_pemohon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_no_hp_kuasa_pemohon">No HP Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('t_form_pk_orang_lain.pk_no_hp_kuasa_pemohon') is-invalid @enderror" 
                            id="pk_no_hp_kuasa_pemohon" name="t_form_pk_orang_lain[pk_no_hp_kuasa_pemohon]" 
                            value="{{ old('t_form_pk_orang_lain.pk_no_hp_kuasa_pemohon') }}">
                        @error('t_form_pk_orang_lain.pk_no_hp_kuasa_pemohon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_email_kuasa_pemohon">Email Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('t_form_pk_orang_lain.pk_email_kuasa_pemohon') is-invalid @enderror" 
                            id="pk_email_kuasa_pemohon" name="t_form_pk_orang_lain[pk_email_kuasa_pemohon]" 
                            value="{{ old('t_form_pk_orang_lain.pk_email_kuasa_pemohon') }}">
                        @error('t_form_pk_orang_lain.pk_email_kuasa_pemohon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pk_upload_nik_kuasa_pemohon">Upload NIK Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('pk_upload_nik_kuasa_pemohon') is-invalid @enderror" 
                            id="pk_upload_nik_kuasa_pemohon" name="pk_upload_nik_kuasa_pemohon" accept="image/*">
                        @error('pk_upload_nik_kuasa_pemohon')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form umum untuk semua kategori -->
                <div class="form-group">
                    <label for="pk_alasan_pengajuan_keberatan">Alasan Pengajuan Keberatan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('t_pernyataan_keberatan.pk_alasan_pengajuan_keberatan') is-invalid @enderror" 
                        id="pk_alasan_pengajuan_keberatan" name="t_pernyataan_keberatan[pk_alasan_pengajuan_keberatan]" 
                        required rows="4">{{ old('t_pernyataan_keberatan.pk_alasan_pengajuan_keberatan') }}</textarea>
                    @error('t_pernyataan_keberatan.pk_alasan_pengajuan_keberatan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Sumber Informasi <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_1" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permohonan Informasi Ditolak" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permohonan Informasi Ditolak' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="sumber_1">Permohonan Informasi Ditolak</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_2" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Form Informasi Berkala Tidak Tersedia" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Form Informasi Berkala Tidak Tersedia' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_2">Form Informasi Berkala Tidak Tersedia</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permintaan Informasi Tidak Ditanggapi" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permintaan Informasi Tidak Ditanggapi' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permintaan Informasi Tidak Ditanggapi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permintaan Informasi Tidak Dipenuhi" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permintaan Informasi Tidak Dipenuhi' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permintaan Informasi Tidak Dipenuhi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Biaya yang Dikenakan Tidak Wajar" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Biaya yang Dikenakan Tidak Wajar' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Biaya yang Dikenakan Tidak Wajar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pernyataan_keberatan.pk_kasus_posisi') is-invalid @enderror" 
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan" 
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan</label>
                    </div>
                    @error('t_pernyataan_keberatan.pk_kasus_posisi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pk_bukti_aduan">Upload Bukti Aduan <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('pk_bukti_aduan') is-invalid @enderror" 
                        id="pk_bukti_aduan" name="pk_bukti_aduan" accept="file/*">
                    @error('pk_bukti_aduan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0"><strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan lebih lanjut jika diperlukan.</p>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Whistle Pernyataan Keberatan</button>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function () {
                // Tampilkan form yang sesuai saat halaman di-load berdasarkan nilai yang tersimpan
                const savedValue = "{{ old('t_pernyataan_keberatan.pk_kategori_pemohon') }}";
                if (savedValue) {
                    showFormBasedOnSelection(savedValue);
                }
                
                $('#pk_kategori_pemohon').change(function () {
                    const selectedValue = $(this).val();
                    showFormBasedOnSelection(selectedValue);
                });
                
                function showFormBasedOnSelection(selectedValue) {
                    // Sembunyikan semua form tambahan
                    $('#formDiriSendiri, #formOrangLain').hide();

                    // Reset required attributes
                    $('#formDiriSendiri input, #formOrangLain input').prop('required', false);

                    // Tampilkan form sesuai pilihan
                    if (selectedValue === 'Orang Lain') {
                        $('#formOrangLain').show();
                        $('#formOrangLain input:not([type="file"])').prop('required', true);
                        $('#pk_upload_nik_pengguna_penginput, #pk_upload_nik_kuasa_pemohon').prop('required', true);
                    } else if (selectedValue === 'Diri Sendiri') {
                        $('#formDiriSendiri').show();
                        $('#formDiriSendiri input:not([type="file"])').prop('required', true);
                        $('#pk_upload_nik_pengguna').prop('required', true);
                    }
                }

                $('#persetujuan').change(function() {
                    if($(this).is(':checked')) {
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        $('#btnSubmit').prop('disabled', true);
                    }
                });
            });
        </script>
    @endpush
@endsection