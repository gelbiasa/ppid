<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\Log\NotifAdminModel;
use App\Models\Log\NotifVerifikatorModel;
use App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PermohonanInformasiModel extends Model
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
        'pi_informasi_yang_dibutuhkan',
        'pi_alasan_permohonan_informasi',
        'pi_sumber_informasi',
        'pi_alamat_sumber_informasi',
        'pi_status',
        'pi_jawaban',
        'pi_alasan_penolakan',
        'pi_sudah_dibaca',
        'isDeleted',
        'created_at',
        'created_by', 
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
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

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $kategoriPemohon = $request->pi_kategori_pemohon;
            $formId = null;
            $notifMessage = '';

            // Handle different types of submissions based on kategori_pemohon
            switch ($kategoriPemohon) {
                case 'Diri Sendiri':
                    list($formId, $notifMessage) = FormPiDiriSendiriModel::createData();
                    break;
                    
                case 'Orang Lain':
                    list($formId, $notifMessage) = FormPiOrangLainModel::createData($request);
                    break;
                    
                case 'Organisasi':
                    list($formId, $notifMessage) = FormPiOrganisasiModel::createData($request);
                    break;
            }

            // Create main permohonan informasi record
            $permohonanInformasi = self::create(array_merge([
                'pi_kategori_pemohon' => $kategoriPemohon,
                'pi_kategori_aduan' => 'online',
                'pi_informasi_yang_dibutuhkan' => $request->pi_informasi_yang_dibutuhkan,
                'pi_alasan_permohonan_informasi' => $request->pi_alasan_permohonan_informasi,
                'pi_sumber_informasi' => implode(', ', $request->pi_sumber_informasi),
                'pi_alamat_sumber_informasi' => $request->pi_alamat_sumber_informasi,
                'pi_status' => 'Masuk',
                'created_by' => session('alias')
            ], $formId));

            // Create notifications
            self::createNotifications($permohonanInformasi->permohonan_informasi_id, $notifMessage);

            // Log the transaction
            self::logTransaction();

            DB::commit();
            return ['success' => true, 'message' => 'Permohonan Informasi berhasil diajukan.'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage()];
        }
    }
    
    public static function updateData($request)
    {

    }

    
    public static function deleteData($request)
    {

    }

    public static function validasiData($request)
    {
        // Validasi dasar untuk semua jenis permohonan
        $validasiDasar = Validator::make($request->all(), [
            'pi_kategori_pemohon' => 'required',
            'pi_informasi_yang_dibutuhkan' => 'required',
            'pi_alasan_permohonan_informasi' => 'required',
            'pi_sumber_informasi' => 'required|array',
            'pi_alamat_sumber_informasi' => 'required',
        ], [
            'pi_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            'pi_informasi_yang_dibutuhkan.required' => 'Informasi yang dibutuhkan wajib diisi',
            'pi_alasan_permohonan_informasi.required' => 'Alasan permohonan informasi wajib diisi',
            'pi_sumber_informasi.required' => 'Sumber informasi wajib diisi',
            'pi_sumber_informasi.array' => 'Format sumber informasi tidak valid',
            'pi_alamat_sumber_informasi.required' => 'Alamat sumber informasi wajib diisi',
        ]);

        if ($validasiDasar->fails()) {
            throw new ValidationException($validasiDasar);
        }

        // Validasi tambahan berdasarkan kategori pemohon
        switch ($request->pi_kategori_pemohon) {
            case 'Orang Lain':
                FormPiOrangLainModel::validasiData($request);
                break;
            case 'Organisasi':
                FormPiOrganisasiModel::validasiData($request);
                break;
        }

        return true;
    }

    private static function createNotifications($formId, $message)
    {
        NotifAdminModel::create([
            'kategori_notif_admin' => 'Permohonan Informasi',
            'notif_admin_form_id' => $formId,
            'pesan_notif_admin' => $message,
            'created_at' => now()
        ]);

        NotifVerifikatorModel::create([
            'kategori_notif_verif' => 'Permohonan Informasi',
            'notif_verifikator_form_id' => $formId,
            'pesan_notif_verif' => $message,
            'created_at' => now()
        ]);
    }

    private static function logTransaction()
    {
        TransactionModel::create([
            'log_transaction_jenis' => 'CREATED',
            'log_transaction_aktivitas' => Auth::user()->nama_pengguna . ' mengajukan form Permohonan Informasi',
            'log_transaction_level' => Auth::user()->level->level_nama,
            'log_transaction_pelaku' => session('alias'),
            'log_transaction_tanggal_aktivitas' => now()
        ]);
    }
}
