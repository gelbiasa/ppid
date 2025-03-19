<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\Log\NotifAdminModel;
use App\Models\Log\NotifVerifikatorModel;
use App\Models\Log\TransactionModel;
use App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use App\Models\SistemInformasi\Timeline\TimelineModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PengaduanMasyarakatModel extends Model
{
    use TraitsModel;

    protected $table = 't_pengaduan_masyarakat';
    protected $primaryKey = 'pengaduan_masyarakat_id';
    protected $fillable = [
        'pm_kategori_aduan',
        'pm_bukti_aduan',
        'pm_nama_tanpa_gelar',
        'pm_nik_pengguna',
        'pm_upload_nik_pengguna',
        'pm_email_pengguna',
        'pm_no_hp_pengguna',
        'pm_jenis_laporan',
        'pm_yang_dilaporkan',
        'pm_jabatan',
        'pm_waktu_kejadian',
        'pm_lokasi_kejadian',
        'pm_kronologis_kejadian',
        'pm_bukti_pendukung',
        'pm_catatan_tambahan',
        'pm_status',
        'pm_jawaban',
        'pm_alasan_penolakan',
        'pm_sudah_dibaca'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('pm_upload_nik_pengguna'),
            'pm_identitas_pelapor'
        );

        $buktiPendukungFile = self::uploadFile(
            $request->file('pm_bukti_pendukung'),
            'pm_bukti_pendukung'
        );

        $buktiAduanFile = self::uploadFile(
            $request->file('pm_bukti_aduan'),
            'pm_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_pengaduan_masyarakat;
            $userLevel = Auth::user()->level->level_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['pm_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pm_email_pengguna'] = Auth::user()->email_pengguna;
                $data['pm_nik_pengguna'] = Auth::user()->nik_pengguna;
                $data['pm_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pm_upload_nik_pengguna'] = $uploadNikPelaporFile;
                $data['pm_bukti_aduan'] = $buktiAduanFile;
            }

            $data['pm_kategori_aduan'] = $kategoriAduan;
            $data['pm_bukti_pendukung'] = $buktiPendukungFile;
            $data['pm_status'] = 'Masuk';

            $saveData = self::create($data);
            $pengaduanId = $saveData->pengaduan_masyarakat_id;

            // Create notifications
            $notifMessage = "{$saveData->pm_nama_tanpa_gelar} Mengajukan Pengaduan Masyarakat";
            NotifAdminModel::createData($pengaduanId, $notifMessage);
            NotifVerifikatorModel::createData($pengaduanId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->pengaduan_masyarakat_id,
                $saveData->pm_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Pengaduan Masyarakat berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan pengaduan');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->level_kode;

        // rules validasi dasar untuk pengaduan masyarakat
        $rules = [
            't_pengaduan_masyarakat.pm_nama_tanpa_gelar' => 'required',
            't_pengaduan_masyarakat.pm_jenis_laporan' => 'required',
            't_pengaduan_masyarakat.pm_yang_dilaporkan' => 'required',
            't_pengaduan_masyarakat.pm_jabatan' => 'required',
            't_pengaduan_masyarakat.pm_waktu_kejadian' => 'required|date',
            't_pengaduan_masyarakat.pm_lokasi_kejadian' => 'required',
            't_pengaduan_masyarakat.pm_kronologis_kejadian' => 'required',
            'pm_bukti_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx,mp4,avi,mov,wmv,3gp,mp3,wav,ogg,m4a|max:100000',
        ];

        // message validasi
        $message = [
            't_pengaduan_masyarakat.pm_nama_tanpa_gelar.required' => 'Nama wajib diisi',
            't_pengaduan_masyarakat.pm_jenis_laporan.required' => 'Jenis laporan wajib diisi',
            't_pengaduan_masyarakat.pm_yang_dilaporkan.required' => 'Yang dilaporkan wajib diisi',
            't_pengaduan_masyarakat.pm_jabatan.required' => 'Jabatan wajib diisi',
            't_pengaduan_masyarakat.pm_waktu_kejadian.required' => 'Waktu kejadian wajib diisi',
            't_pengaduan_masyarakat.pm_waktu_kejadian.date' => 'Format tanggal tidak valid',
            't_pengaduan_masyarakat.pm_lokasi_kejadian.required' => 'Lokasi kejadian wajib diisi',
            't_pengaduan_masyarakat.pm_kronologis_kejadian.required' => 'Kronologis kejadian wajib diisi',
            'pm_bukti_pendukung.required' => 'Upload Bukti Aduan penginput wajib diisi',
            'pm_bukti_pendukung.file' => 'Bukti pendukung harus berupa file',
            'pm_bukti_pendukung.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'pm_bukti_pendukung.max' => 'Ukuran file tidak boleh lebih dari 100MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        if ($userLevel === 'ADM') {
            $rules['t_pengaduan_masyarakat.pm_nik_pengguna'] = 'required|numeric|digits:16';
            $rules['t_pengaduan_masyarakat.pm_email_pengguna'] = 'required|email';
            $rules['t_pengaduan_masyarakat.pm_no_hp_pengguna'] = 'required';
            $rules['pm_upload_nik_pengguna'] = 'required|image|max:10240';
            $rules['pm_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['t_pengaduan_masyarakat.pm_nik_pengguna.required'] = 'NIK wajib diisi';
            $message['t_pengaduan_masyarakat.pm_nik_pengguna.numeric'] = 'NIK harus berupa angka';
            $message['t_pengaduan_masyarakat.pm_nik_pengguna.digits'] = 'NIK harus 16 digit';
            $message['t_pengaduan_masyarakat.pm_email_pengguna.required'] = 'Email wajib diisi';
            $message['t_pengaduan_masyarakat.pm_email_pengguna.email'] = 'Format email tidak valid';
            $message['t_pengaduan_masyarakat.pm_no_hp_pengguna.required'] = 'Nomor HP wajib diisi';
            $message['pm_upload_nik_pengguna.required'] = 'Upload NIK wajib diisi';
            $message['pm_upload_nik_pengguna.image'] = 'File harus berupa gambar';
            $message['pm_upload_nik_pengguna.max'] = 'Ukuran file tidak boleh lebih dari 10MB';
            $message['pm_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pm_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pm_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pm_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Lakukan validasi
        $validator = Validator::make($request->all(), $rules, $message);

        // Lemparkan exception jika validasi gagal
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getTimeline()
    {
        // Ambil ID kategori form untuk 'Pengaduan Masyarakat'
        $kategoriForm = KategoriFormModel::where('kf_nama', 'Pengaduan Masyarakat')
            ->where('isDeleted', 0)
            ->first();

        // Jika kategori form ditemukan, cari timeline terkait
        $timeline = null;
        if ($kategoriForm) {
            $timeline = TimelineModel::with('langkahTimeline')
                ->where('fk_m_kategori_form', $kategoriForm->kategori_form_id)
                ->where('isDeleted', 0)
                ->first();
        }

        return $timeline;
    }

    public static function getKetentuanPelaporan()
    {
        // Ambil ID kategori form untuk 'Pengaduan Masyarakat'
        $kategoriForm = KategoriFormModel::where('kf_nama', 'Pengaduan Masyarakat')
            ->where('isDeleted', 0)
            ->first();

        // Jika kategori form ditemukan, cari ketentuan pelaporan terkait
        $ketentuanPelaporan = null;
        if ($kategoriForm) {
            $ketentuanPelaporan = DB::table('m_ketentuan_pelaporan')
                ->where('fk_m_kategori_form', $kategoriForm->kategori_form_id)
                ->where('isDeleted', 0)
                ->first();
        }

        return $ketentuanPelaporan;
    }
}
