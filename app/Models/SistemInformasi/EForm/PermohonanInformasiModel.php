<?php

namespace App\Models\SistemInformasi\EForm;

error_reporting(E_ALL);

use App\Models\BaseModel;
use App\Models\Log\NotifAdminModel;
use App\Models\Log\NotifVerifikatorModel;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PermohonanInformasiModel extends BaseModel
{
    use HasFactory, SoftDeletes;

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

    // Konstruktor untuk menggabungkan field umum
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

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

    public static function createData($request)
    {
        $fileName = self::uploadFile(
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
                $data['pi_bukti_aduan'] = $fileName;
            }

            // Handle different types of submissions based on kategori_pemohon
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
            $data['pi_bukti_aduan'] = $fileName;
            $data['pi_status'] = 'Masuk';

            // Tetapkan nilai child primary key ke field relasi yang sesuai
            // Ini perbaikan untuk masalah pertama
            $data[$child['pkField']] = $child['id'];

            $saveData = self::create($data);

            $notifMessage = $child['message'];

            // Perbaikan untuk masalah ketiga: gunakan ID permohonan_informasi untuk notifikasi
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

            $result = [
                'success' => true,
                'message' => 'Permohonan Informasi berhasil diajukan.',
                'data' => $saveData
            ];

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollback();
            self::removeFile($fileName);
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ];
        } catch (\Exception $e) {
            DB::rollback();
            self::removeFile($fileName);

            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage()];
        }
    }

    public static function validasiData($request)
    {
        $userLevel = Auth::user()->level->level_kode;

        // Build validation rules array
        $rules = [
            't_permohonan_informasi.pi_kategori_pemohon' => 'required',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan' => 'required',
            't_permohonan_informasi.pi_alasan_permohonan_informasi' => 'required',
            't_permohonan_informasi.pi_sumber_informasi' => 'required',
            't_permohonan_informasi.pi_alamat_sumber_informasi' => 'required',

        ];

        // Add bukti_aduan validation for ADM users
        if ($userLevel === 'ADM') {
            $rules['pi_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
        }

        $messages = [
            't_permohonan_informasi.pi_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan.required' => 'Informasi yang dibutuhkan wajib diisi',
            't_permohonan_informasi.pi_alasan_permohonan_informasi.required' => 'Alasan permohonan informasi wajib diisi',
            't_permohonan_informasi.pi_sumber_informasi.required' => 'Sumber informasi wajib diisi',
            't_permohonan_informasi.pi_alamat_sumber_informasi.required' => 'Alamat sumber informasi wajib diisi',
            'pi_bukti_aduan.required' => 'Bukti aduan wajib diupload untuk Admin',
            'pi_bukti_aduan.file' => 'Bukti aduan harus berupa file',
            'pi_bukti_aduan.mimes' => 'Format file bukti aduan tidak valid. Format yang diizinkan: PDF, JPG, JPEG, PNG, SVG, DOC, DOCX',
            'pi_bukti_aduan.max' => 'Ukuran file bukti aduan maksimal 10MB',
        ];

        $validasiDasar = Validator::make($request->all(), $rules, $messages);

        if ($validasiDasar->fails()) {
            throw new ValidationException($validasiDasar);
        }

        // Validasi tambahan berdasarkan kategori pemohon
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

    private static function uploadFile($file, $prefix)
    {
        $fileName = $prefix . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        return $fileName;
    }

    private static function removeFile($fileName)
    {
        if ($fileName) {
            $filePath = storage_path('app/public/' . $fileName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
