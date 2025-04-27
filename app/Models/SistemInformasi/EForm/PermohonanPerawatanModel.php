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

class PermohonanPerawatanModel extends Model
{
    use TraitsModel;

    protected $table = 't_permohonan_perawatan';
    protected $primaryKey = 'permohonan_perawatan_id';
    protected $fillable = [
        'pp_kategori_aduan',
        'pp_bukti_aduan',
        'pp_nama_pengguna',
        'pp_no_hp_pengguna',
        'pp_email_pengguna',
        'pp_unit_kerja',
        'pp_perawatan_yang_diusulkan',
        'pp_keluhan_kerusakan',
        'pp_lokasi_perawatan',
        'pp_foto_kondisi',
        'pp_status',
        'pp_jawaban',
        'pp_alasan_penolakan',
        'pp_sudah_dibaca'
        
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        // Periksa apakah ada file foto kondisi yang diupload
        $fotoKondisiSarpras = null;
        if ($request->hasFile('pp_foto_kondisi')) {
            $fotoKondisiSarpras = self::uploadFile(
                $request->file('pp_foto_kondisi'),
                'pp_foto_kondisi'
            );
        }

        $buktiAduanFile = self::uploadFile(
            $request->file('pp_bukti_aduan'),
            'pp_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_permohonan_perawatan;
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['pp_nama_pengguna'] = Auth::user()->nama_pengguna;
                $data['pp_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pp_email_pengguna'] = Auth::user()->email_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pp_bukti_aduan'] = $buktiAduanFile;
            }

            $data['pp_kategori_aduan'] = $kategoriAduan;
            
            // Tetapkan nilai foto kondisi jika ada
            if ($fotoKondisiSarpras) {
                $data['pp_foto_kondisi'] = $fotoKondisiSarpras;
            }
            
            $data['pp_status'] = 'Masuk';

            $saveData = self::create($data);
            $permohonanPerawatan = $saveData->permohonan_perawatan_id;

            // Create notifications
            $notifMessage = "{$saveData->pp_nama_tanpa_gelar} Mengajukan Permohonan Perawatan Sarana Prasarana";
            NotifAdminModel::createData($permohonanPerawatan, $notifMessage);
            NotifVerifikatorModel::createData($permohonanPerawatan, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->permohonan_perawatan_id,
                $saveData->pp_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Permohonan Perawatan Sarana Prasarana berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($fotoKondisiSarpras) {
                self::removeFile($fotoKondisiSarpras);
            }
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($fotoKondisiSarpras) {
                self::removeFile($fotoKondisiSarpras);
            }
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Permohonan Perawatan Sarana Prasarana');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->hak_akses_kode;

        // rules validasi dasar untuk Permohonan Perawatan Sarana Prasarana
        $rules = [
            't_permohonan_perawatan.pp_unit_kerja' => 'required',
            't_permohonan_perawatan.pp_perawatan_yang_diusulkan' => 'required',
            't_permohonan_perawatan.pp_keluhan_kerusakan' => 'required',
            't_permohonan_perawatan.pp_lokasi_perawatan' => 'required',
            'pp_foto_kondisi' => 'nullable|image|max:10240', // Diubah dari required menjadi nullable
        ];

        // message validasi
        $message = [
            't_permohonan_perawatan.pp_unit_kerja.required' => 'Unit Kerja wajib diisi',
            't_permohonan_perawatan.pp_perawatan_yang_diusulkan.required' => 'Perawatan yang diusulkan wajib diisi',
            't_permohonan_perawatan.pp_keluhan_kerusakan.required' => 'Keluhan Kerusakan wajib diisi',
            't_permohonan_perawatan.pp_lokasi_perawatan.required' => 'Lokasi perawatan wajib diisi',
            'pp_foto_kondisi.image' => 'Foto Kondisi harus berupa gambar',
            'pp_foto_kondisi.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'pp_foto_kondisi.max' => 'Ukuran file tidak boleh lebih dari 10MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        if ($userLevel === 'ADM') {
            $rules['t_permohonan_perawatan.pp_nama_pengguna'] = 'required';
            $rules['t_permohonan_perawatan.pp_email_pengguna'] = 'required|email';
            $rules['t_permohonan_perawatan.pp_no_hp_pengguna'] = 'required';
            $rules['pp_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['t_permohonan_perawatan.pp_nama_pengguna.required'] = 'Nama Pengusul Wajib diisi';
            $message['t_permohonan_perawatan.pp_email_pengguna.required'] = 'Email Pengusul wajib diisi';
            $message['t_permohonan_perawatan.pp_email_pengguna.email'] = 'Format email tidak valid';
            $message['t_permohonan_perawatan.pp_no_hp_pengguna.required'] = 'Nomor HP Pengusul wajib diisi';
            $message['pp_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pp_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pp_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pp_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
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
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Permohonan Perawatan Sarana Prasarana');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Permohonan Perawatan Sarana Prasarana');
    }
}
