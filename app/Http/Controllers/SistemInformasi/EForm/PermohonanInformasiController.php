<?php

namespace App\Http\Controllers\SistemInformasi\EForm;

use App\Http\Controllers\Controller;
use App\Models\SistemInformasi\EForm\FormPiDiriSendiriModel;
use App\Models\SistemInformasi\EForm\FormPiOrangLainModel;
use App\Models\SistemInformasi\EForm\FormPiOrganisasiModel;
use App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermohonanInformasiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi'; // Set the active menu

        return view('SistemInformasi/EForm/PermohonanInformasi.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function formPermohonanInformasi()
    {
        $breadcrumb = (object) [
            'title' => 'Permohonan Informasi',
            'list' => ['Home', 'Permohonan Informasi', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'PermohonanInformasi';

        return view('SistemInformasi/EForm/PermohonanInformasi.pengisianForm', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function storePermohonanInformasi(Request $request)
    {
        // Validasi request
        $request->validate([
            'pi_kategori_pemohon' => 'required',
            'pi_informasi_yang_dibutuhkan' => 'required',
            'pi_alasan_permohonan_informasi' => 'required',
            'pi_sumber_informasi' => 'required|array',
            'pi_alamat_sumber_informasi' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $kategoriPemohon = $request->pi_kategori_pemohon;
            $formId = null;

            if ($kategoriPemohon === 'Diri Sendiri') {
                // Simpan data diri sendiri
                $diriSendiri = FormPiDiriSendiriModel::create([
                    'pi_nama_pengguna' => Auth::user()->nama_pengguna,
                    'pi_alamat_pengguna' => Auth::user()->alamat_pengguna,
                    'pi_no_hp_pengguna' => Auth::user()->no_hp_pengguna,
                    'pi_email_pengguna' => Auth::user()->email_pengguna,
                    'pi_upload_nik_pengguna' => Auth::user()->upload_nik_pengguna,
                    'created_by' => session('alias')
                ]);
                $formId = ['fk_t_form_pi_diri_sendiri' => $diriSendiri->form_pi_diri_sendiri_id];
            } elseif ($kategoriPemohon === 'Orang Lain') {
                // Validasi tambahan untuk orang lain
                $request->validate([
                    'pi_upload_nik_pengguna_informasi' => 'required|image|max:10240',
                    'pi_nama_pengguna_informasi' => 'required',
                    'pi_alamat_pengguna_informasi' => 'required',
                    'pi_no_hp_pengguna_informasi' => 'required',
                    'pi_email_pengguna_informasi' => 'required|email',
                ]);

                // Upload file
                $file = $request->file('pi_upload_nik_pengguna_informasi');
                $fileName = 'pi_ol_upload_nik/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public', $fileName);

                // Simpan data orang lain
                $orangLain = FormPiOrangLainModel::create([
                    'pi_nama_pengguna_penginput' => Auth::user()->nama_pengguna,
                    'pi_alamat_pengguna_penginput' => Auth::user()->alamat_pengguna,
                    'pi_no_hp_pengguna_penginput' => Auth::user()->no_hp_pengguna,
                    'pi_email_pengguna_penginput' => Auth::user()->email_pengguna,
                    'pi_upload_nik_pengguna_penginput' => Auth::user()->upload_nik_pengguna,
                    'pi_nama_pengguna_informasi' => $request->pi_nama_pengguna_informasi,
                    'pi_alamat_pengguna_informasi' => $request->pi_alamat_pengguna_informasi,
                    'pi_no_hp_pengguna_informasi' => $request->pi_no_hp_pengguna_informasi,
                    'pi_email_pengguna_informasi' => $request->pi_email_pengguna_informasi,
                    'pi_upload_nik_pengguna_informasi' => $fileName,
                    'created_by' => session('alias')
                ]);
                $formId = ['fk_t_form_pi_orang_lain' => $orangLain->form_pi_orang_lain_id];
            } elseif ($kategoriPemohon === 'Organisasi') {
                // Validasi tambahan untuk organisasi
                $request->validate([
                    'pi_identitas_narahubung' => 'required|image|max:10240',
                    'pi_nama_organisasi' => 'required',
                    'pi_no_telp_organisasi' => 'required',
                    'pi_email_atau_medsos_organisasi' => 'required',
                    'pi_nama_narahubung' => 'required',
                    'pi_no_telp_narahubung' => 'required',
                ]);

                // Upload file
                $file = $request->file('pi_identitas_narahubung');
                $fileName = 'pi_organisasi_identitas/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public', $fileName);

                // Simpan data organisasi
                $organisasi = FormPiOrganisasiModel::create([
                    'pi_nama_organisasi' => $request->pi_nama_organisasi,
                    'pi_no_telp_organisasi' => $request->pi_no_telp_organisasi,
                    'pi_email_atau_medsos_organisasi' => $request->pi_email_atau_medsos_organisasi,
                    'pi_nama_narahubung' => $request->pi_nama_narahubung,
                    'pi_no_telp_narahubung' => $request->pi_no_telp_narahubung,
                    'pi_identitas_narahubung' => $fileName,
                    'created_by' => session('alias')
                ]);
                $formId = ['fk_t_form_pi_organisasi' => $organisasi->form_pi_organisasi_id];
            }

            // Simpan permohonan informasi
            PermohonanInformasiModel::create(array_merge([
                'pi_kategori_pemohon' => $kategoriPemohon,
                'pi_kategori_aduan' => 'online',
                'pi_informasi_yang_dibutuhkan' => $request->pi_informasi_yang_dibutuhkan,
                'pi_alasan_permohonan_informasi' => $request->pi_alasan_permohonan_informasi,
                'pi_sumber_informasi' => implode(', ', $request->pi_sumber_informasi),
                'pi_alamat_sumber_informasi' => $request->pi_alamat_sumber_informasi,
                'pi_status' => 'Masuk',
                'created_by' => session('alias')
            ], $formId));

            DB::commit();
            return redirect('/SistemInformasi/EForm/PermohonanInformasi')
                ->with('success', 'Permohonan Informasi berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
