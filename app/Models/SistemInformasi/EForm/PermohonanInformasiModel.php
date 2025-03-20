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

class PermohonanInformasiModel extends Model
{
    use TraitsModel;

    protected $table = 't_permohonan_informasi';
    protected $primaryKey = 'permohonan_informasi_id';
    protected $fillable = [
        'fk_t_form_pi_diri_sendiri',
        'fk_t_form_pi_orang_lain',
        'fk_t_form_pi_organisasi',
        'pi_kategori_pemohon',
        'pi_kategori_aduan',
        'pi_bukti_aduan',
        'pi_informasi_yang_dibutuhkan',
        'pi_alasan_permohonan_informasi',
        'pi_sumber_informasi',
        'pi_alamat_sumber_informasi',
        'pi_status',
        'pi_jawaban',
        'pi_alasan_penolakan',
        'pi_sudah_dibaca'
    ];

    public function PiDiriSendiri()
    {
        return $this->belongsTo(FormPiDiriSendiriModel::class, 'fk_t_form_pi_diri_sendiri', 'form_pi_diri_sendiri_id');
    }

    public function PiOrangLain()
    {
        return $this->belongsTo(FormPiOrangLainModel::class, 'fk_t_form_pi_orang_lain', 'form_pi_orang_lain_id');
    }

    public function PiOrganisasi()
    {
        return $this->belongsTo(FormPiOrganisasiModel::class, 'fk_t_form_pi_organisasi', 'form_pi_organisasi_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        //
    }

    public static function createData($request)
    {
        $buktiAduanFile = self::uploadFile(
            $request->file('pi_bukti_aduan'),
            'pi_bukti_aduan'
        );

        $notifMessage = '';
        try {
            $data = $request->t_permohonan_informasi;
            $kategoriPemohon = $data['pi_kategori_pemohon'];
            $userLevel = Auth::user()->level->level_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            if ($userLevel === 'ADM') {
                $data['pi_bukti_aduan'] = $buktiAduanFile;
            }

            switch ($kategoriPemohon) {

                case 'Diri Sendiri':
                    $child = FormPiDiriSendiriModel::createData($request);
                    break;

                case 'Orang Lain':
                    $child = FormPiOrangLainModel::createData($request);
                    break;

                case 'Organisasi':
                    $child = FormPiOrganisasiModel::createData($request);
                    break;
            }

            DB::beginTransaction();

            $data['pi_kategori_pemohon'] = $kategoriPemohon;
            $data['pi_kategori_aduan'] = $kategoriAduan;
            $data['pi_status'] = 'Masuk';

            $data[$child['pkField']] = $child['id'];
            $saveData = self::create($data);
            $notifMessage = $child['message'];
            $permohonanId = $saveData->permohonan_informasi_id;

            // Create notifications dengan permohonan_informasi_id
            NotifAdminModel::createData($permohonanId, $notifMessage);
            NotifVerifikatorModel::createData($permohonanId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->permohonan_informasi_id,
                $saveData->pi_informasi_yang_dibutuhkan
            );

            $result = self::responFormatSukses($saveData, 'Permohonan Informasi berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan permohonan');
        }
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function validasiData($request)
    {
        // rules validasi dasar untuk permohonan informasi
        $rules = [
            't_permohonan_informasi.pi_kategori_pemohon' => 'required',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan' => 'required',
            't_permohonan_informasi.pi_alasan_permohonan_informasi' => 'required',
            't_permohonan_informasi.pi_sumber_informasi' => 'required',
            't_permohonan_informasi.pi_alamat_sumber_informasi' => 'required',
        ];

        // message validasi dasar
        $message = [
            't_permohonan_informasi.pi_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan.required' => 'Informasi yang dibutuhkan wajib diisi',
            't_permohonan_informasi.pi_alasan_permohonan_informasi.required' => 'Alasan permohonan informasi wajib diisi',
            't_permohonan_informasi.pi_sumber_informasi.required' => 'Sumber informasi wajib diisi',
            't_permohonan_informasi.pi_alamat_sumber_informasi.required' => 'Alamat sumber informasi wajib diisi',
        ];

        // Tambahkan validasi untuk admin jika diperlukan
        if (Auth::user()->level->level_kode === 'ADM') {
            $rules['pi_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['pi_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pi_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pi_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pi_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Validasi berdasarkan kategori pemohon
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi detail berdasarkan kategori pemohon
        $kategoriPemohon = $request->t_permohonan_informasi['pi_kategori_pemohon'];
        switch ($kategoriPemohon) {
            case 'Diri Sendiri':
                FormPiDiriSendiriModel::validasiData($request);
                break;
            case 'Orang Lain':
                FormPiOrangLainModel::validasiData($request);
                break;
            case 'Organisasi':
                FormPiOrganisasiModel::validasiData($request);
                break;
        }

        return true;
    }

    public static function getTimeline()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Permohonan Informasi');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Permohonan Informasi');
    }
}
