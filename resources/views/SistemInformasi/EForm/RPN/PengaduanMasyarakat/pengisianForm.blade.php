<!-- SistemInformasi/EForm/RPN/PengaduanMasyarakat/pengisianForm.blade.php-->

@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PengaduanMasyarakat') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Pengaduan Masyarakat </strong></h3>
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

            <form action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PengaduanMasyarakat/createData') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                
                <!-- Data Pelapor -->
                <h4 class="mb-3">Data Pelapor</h4>
                
                <div class="alert alert-info">
                    <p class="mb-0">Data Anda seperti Nomor Handpohone, Email, NIK, Foto Identitas akan digunakan sebagai data pelapor secara otomatis.</p>
                </div>

                <!-- Preview data pengguna yang sudah login -->
                <div class="card mb-4">
                    <div class="card-body">
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
                    <label for="pm_nama_tanpa_gelar">Nama Lengkap (Tanpa Gelar)<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_pengaduan_masyarakat.pm_nama_tanpa_gelar') is-invalid @enderror" 
                        id="pm_nama_tanpa_gelar" name="t_pengaduan_masyarakat[pm_nama_tanpa_gelar]"
                        value="{{ old('t_pengaduan_masyarakat.pm_nama_tanpa_gelar') }}">
                    @error('t_pengaduan_masyarakat.pm_nama_tanpa_gelar')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Data Pengaduan -->
                <h4 class="mb-3 mt-4">Detail Pengaduan</h4>
                
                <div class="form-group">
                    <label>Jenis Laporan <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_1" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Pelanggaran Disiplin Pegawai" 
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pelanggaran Disiplin Pegawai' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_1">Pelanggaran Disiplin Pegawai</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_2" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Penyalahgunaan Wewenang / Mal Administrasi"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Penyalahgunaan Wewenang / Mal Administrasi' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_2">Penyalahgunaan Wewenang / Mal Administrasi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_3" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Pungutan Liar, Percaloan, dan Pengurusan Dokumen" 
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pungutan Liar, Percaloan, dan Pengurusan Dokumen' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_3">Pungutan Liar, Percaloan, dan Pengurusan Dokumen</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_4" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_4">Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_5" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Pengadaan Barang dan Jasa"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pengadaan Barang dan Jasa' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_5">Pengadaan Barang dan Jasa</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_6" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Narkoba"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Narkoba' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_6">Narkoba</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jenis_laporan') is-invalid @enderror" 
                            type="radio" id="jenis_laporan_7" name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Pelayanan Publik"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pelayanan Publik' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_7">Pelayanan Publik</label>
                    </div>
                    @error('t_pengaduan_masyarakat.pm_jenis_laporan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_yang_dilaporkan">Pihak/Orang yang Dilaporkan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_pengaduan_masyarakat.pm_yang_dilaporkan') is-invalid @enderror" 
                        id="pm_yang_dilaporkan" name="t_pengaduan_masyarakat[pm_yang_dilaporkan]"
                        value="{{ old('t_pengaduan_masyarakat.pm_yang_dilaporkan') }}">
                    @error('t_pengaduan_masyarakat.pm_yang_dilaporkan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Jabatan<span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jabatan') is-invalid @enderror" 
                            type="radio" id="jabatan_1" name="t_pengaduan_masyarakat[pm_jabatan]"
                            value="Staff" 
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Staff' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_1">Staff</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jabatan') is-invalid @enderror" 
                            type="radio" id="jabatan_2" name="t_pengaduan_masyarakat[pm_jabatan]"
                            value="Dosen"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Dosen' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_2">Dosen</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input @error('t_pengaduan_masyarakat.pm_jabatan') is-invalid @enderror" 
                            type="radio" id="jabatan_3" name="t_pengaduan_masyarakat[pm_jabatan]"
                            value="Tidak tahu"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Tidak tahu' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_3">Tidak tahu</label>
                    </div>
                    @error('t_pengaduan_masyarakat.pm_jabatan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_waktu_kejadian">Waktu Kejadian <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('t_pengaduan_masyarakat.pm_waktu_kejadian') is-invalid @enderror" 
                        id="pm_waktu_kejadian" name="t_pengaduan_masyarakat[pm_waktu_kejadian]"
                        value="{{ old('t_pengaduan_masyarakat.pm_waktu_kejadian') }}">
                    @error('t_pengaduan_masyarakat.pm_waktu_kejadian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_lokasi_kejadian">Lokasi Kejadian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_pengaduan_masyarakat.pm_lokasi_kejadian') is-invalid @enderror" 
                        id="pm_lokasi_kejadian" name="t_pengaduan_masyarakat[pm_lokasi_kejadian]"
                        value="{{ old('t_pengaduan_masyarakat.pm_lokasi_kejadian') }}">
                    @error('t_pengaduan_masyarakat.pm_lokasi_kejadian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_kronologis_kejadian">Kronologis Kejadian <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('t_pengaduan_masyarakat.pm_kronologis_kejadian') is-invalid @enderror" 
                        id="pm_kronologis_kejadian" name="t_pengaduan_masyarakat[pm_kronologis_kejadian]" 
                        required rows="4">{{ old('t_pengaduan_masyarakat.pm_kronologis_kejadian') }}</textarea>
                    @error('t_pengaduan_masyarakat.pm_kronologis_kejadian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_bukti_pendukung">Bukti Pendukung <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('pm_bukti_pendukung') is-invalid @enderror" 
                        id="pm_bukti_pendukung" name="pm_bukti_pendukung">
                    <small class="form-text text-muted">
                        Format yang diizinkan: 
                        <br>- Dokumen: PDF, DOC, DOCX
                        <br>- Gambar: JPG, JPEG, PNG, SVG
                        <br>- Video: MP4, AVI, MOV, WMV, 3GP
                        <br>- Audio/Rekaman: MP3, WAV, OGG, M4A
                        <br>Maksimal 100MB.
                    </small>
                    @error('pm_bukti_pendukung')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pm_catatan_tambahan">Catatan Tambahan (opsional)</label>
                    <textarea class="form-control @error('t_pengaduan_masyarakat.pm_catatan_tambahan') is-invalid @enderror" 
                        id="pm_catatan_tambahan" name="t_pengaduan_masyarakat[pm_catatan_tambahan]" 
                        required rows="4">{{ old('t_pengaduan_masyarakat.pm_catatan_tambahan') }}</textarea>
                    @error('t_pengaduan_masyarakat.pm_catatan_tambahan')
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
                
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Pengaduan</button>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function () {
                // Enable/disable submit button based on checkbox
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