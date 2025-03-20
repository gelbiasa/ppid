<!-- pengisian form halaman responden -->
@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PernyataanKeberatan') }}" class="btn btn-secondary">
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

            <form action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PernyataanKeberatan/createData') }}" method="POST"
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

                <!-- Form untuk Diri Sendiri Bagian Responden -->
                <div id="formDiriSendiri" style="display: none;">
                    <div class="alert alert-info">
                        Data Diri Anda seperti Nama lengkap, Alamat Email, No Hp, Foto Identitas(NIK), akan digunakan sebagai data pengajuan.
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nama Lengkap:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->nama_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Alamat:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->alamat_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->email_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nomor HP:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->no_hp_pengguna }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form untuk Orang Lain Bagian Responden -->
                <div id="formOrangLain" style="display: none;">
                    <div class="alert alert-info">
                        Data diri Anda seperti Nama lengkap, Alamat Email, No Hp, Foto Identitas(NIK) akan digunakan sebagai data pelapor. Silakan isi data pengguna informasi di bawah ini.
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nama Lengkap:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->nama_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Alamat:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->alamat_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->email_pengguna }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nomor HP:</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ Auth::user()->no_hp_pengguna }}
                                </div>
                            </div>
                        </div>
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
                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0"><strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan lebih lanjut jika diperlukan.</p>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Permohonan Informasi</button>
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
                    $('#formOrangLain input').prop('required', false);

                    // Tampilkan form sesuai pilihan
                    if (selectedValue === 'Orang Lain') {
                        $('#formOrangLain').show();
                        $('#formOrangLain input:not([type="file"])').prop('required', true);
                        $('#pk_upload_nik_pengguna_penginput, #pk_upload_nik_kuasa_pemohon').prop('required', true);
                    } else if (selectedValue === 'Diri Sendiri') {
                        $('#formDiriSendiri').show();
                    }
                }
            });

            $('#persetujuan').change(function() {
                    if($(this).is(':checked')) {
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        $('#btnSubmit').prop('disabled', true);
                    }
                });
        </script>
    @endpush
@endsection