<!-- SistemInformasi/EForm/ADM/PermohonanPerawatan/pengisianForm.blade.php-->

@extends('layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanPerawatan') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Perawatan Sarana Prasarana </strong></h3>
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

            <form action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanPerawatan/createData') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                
                <!-- Data Pelapor -->
                <h4 class="mb-3">Data Pelapor</h4>
                
                <div class="form-group">
                    <label for="pp_nama_pengguna">Nama Lengkap Pengusul<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_perawatan.pp_nama_pengguna') is-invalid @enderror" 
                        id="pp_nama_pengguna" name="t_permohonan_perawatan[pp_nama_pengguna]"
                        value="{{ old('t_permohonan_perawatan.pp_nama_pengguna') }}">
                    @error('t_permohonan_perawatan.pp_nama_pengguna')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pp_no_hp_pengguna">Nomor HP Pengusul<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_perawatan.pp_no_hp_pengguna') is-invalid @enderror" 
                        id="pp_no_hp_pengguna" name="t_permohonan_perawatan[pp_no_hp_pengguna]"
                        value="{{ old('t_permohonan_perawatan.pp_no_hp_pengguna') }}">
                    @error('t_permohonan_perawatan.pp_no_hp_pengguna')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="pp_email_pengguna">Email Pengusul<span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('t_permohonan_perawatan.pp_email_pengguna') is-invalid @enderror" 
                        id="pp_email_pengguna" name="t_permohonan_perawatan[pp_email_pengguna]"
                        value="{{ old('t_permohonan_perawatan.pp_email_pengguna') }}">
                    @error('t_permohonan_perawatan.pp_email_pengguna')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pp_unit_kerja">Unit Kerja Pengusul <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_perawatan.pp_unit_kerja') is-invalid @enderror" 
                        id="pp_unit_kerja" name="t_permohonan_perawatan[pp_unit_kerja]"
                        value="{{ old('t_permohonan_perawatan.pp_unit_kerja') }}">
                    @error('t_permohonan_perawatan.pp_unit_kerja')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Data Permohonan Perawatan Sarana Prasarana -->
                <h4 class="mb-3 mt-4">Detail Permohonan Perawatan Sarana Prasarana</h4>
                
                <div class="form-group">
                    <label for="pp_perawatan_yang_diusulkan">Perawatan Yang Diusulkan<span class="text-danger">*</span></label>
                    <select class="form-control @error('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') is-invalid @enderror" 
                        id="pp_perawatan_yang_diusulkan" 
                        name="t_permohonan_perawatan[pp_perawatan_yang_diusulkan]" required>
                        <option value="">-- Pilih Jenis Perawatan --</option>
                        <option value="Alat Angkutan" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Angkutan' ? 'selected' : '' }}>Alat Angkutan</option>
                        <option value="Alat Bengkel dan Alat Ukur" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Bengkel dan Alat Ukur' ? 'selected' : '' }}>Alat Bengkel dan Alat Ukur</option>
                        <option value="Alat Besar" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Besar' ? 'selected' : '' }}>Alat Besar</option>
                        <option value="Alat Eksploarasi" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Eksploarasi' ? 'selected' : '' }}>Alat Eksploarasi</option>
                        <option value="Alat Kantor dan Rumah Tangga" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Kantor dan Rumah Tangga' ? 'selected' : '' }}>Alat Kantor dan Rumah Tangga</option>
                        <option value="Alat Laboratorium" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Laboratorium' ? 'selected' : '' }}>Alat Laboratorium</option>
                        <option value="Alat Peraga" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Peraga' ? 'selected' : '' }}>Alat Peraga</option>
                        <option value="Alat Produksi, Pengolahan dan Pemurnian" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Produksi, Pengolahan dan Pemurnian' ? 'selected' : '' }}>Alat Produksi, Pengolahan dan Pemurnian</option>
                        <option value="Alat Studio, Komunikasi dan Pemancar" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Studio, Komunikasi dan Pemancar' ? 'selected' : '' }}>Alat Studio, Komunikasi dan Pemancar</option>
                        <option value="Bangunan Air" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Bangunan Air' ? 'selected' : '' }}>Bangunan Air</option>
                        <option value="Bangunan Gedung" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Bangunan Gedung' ? 'selected' : '' }}>Bangunan Gedung</option>
                        <option value="Jalan dan Jembatan" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Jalan dan Jembatan' ? 'selected' : '' }}>Jalan dan Jembatan</option>
                        <option value="Jaringan" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Jaringan' ? 'selected' : '' }}>Jaringan</option>
                        <option value="Peralatan Proses/Produksi" {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Peralatan Proses/Produksi' ? 'selected' : '' }}>Peralatan Proses/Produksi</option>
                    </select>
                    @error('t_permohonan_perawatan.pp_perawatan_yang_diusulkan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pp_keluhan_kerusakan">Keluhan Kerusakan<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_perawatan.pp_keluhan_kerusakan') is-invalid @enderror" 
                        id="pp_keluhan_kerusakan" name="t_permohonan_perawatan[pp_keluhan_kerusakan]"
                        required rows="4"{{ old('t_permohonan_perawatan.pp_keluhan_kerusakan') }}>
                    @error('t_permohonan_perawatan.pp_keluhan_kerusakan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pp_lokasi_perawatan">Lokasi Perawatan<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('t_permohonan_perawatan.pp_lokasi_perawatan') is-invalid @enderror" 
                        id="pp_lokasi_perawatan" name="t_permohonan_perawatan[pp_lokasi_perawatan]"
                        value="{{ old('t_permohonan_perawatan.pp_lokasi_perawatan') }}">
                    @error('t_permohonan_perawatan.pp_lokasi_perawatan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pp_foto_kondisi">Foto Kondisi (Opsional)</label>
                    <input type="file" class="form-control @error('pp_foto_kondisi') is-invalid @enderror" 
                        id="pp_foto_kondisi" name="pp_foto_kondisi" accept="file/*">
                    <small class="form-text text-muted">Unggah foto kondisi jika diperlukan</small>
                    @error('pp_foto_kondisi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pp_bukti_aduan">Upload Bukti Aduan <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('pp_bukti_aduan') is-invalid @enderror" 
                        id="pp_bukti_aduan" name="pp_bukti_aduan" accept="file/*">
                    @error('pp_bukti_aduan')
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
                
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Permohonan Perawatan Sarana Prasarana</button>
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