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

class PernyataanKeberatanModel extends Model
{
    use TraitsModel;

    protected $table = 't_pernyataan_keberatan';
    protected $primaryKey = 'pernyataan_keberatan_id';
    protected $fillable = [
        'fk_t_form_pk_diri_sendiri',
        'fk_t_form_pk_orang_lain',
        'pk_kategori_pemohon',
        'pk_kategori_aduan',
        'pk_bukti_aduan',
        'pk_alasan_pengajuan_keberatan',
        'pk_kasus_posisi',
        'pk_status',
        'pk_jawaban',
        'pk_alasan_penolakan',
        'pk_sudah_dibaca'
    ];

    public function PkDiriSendiri()
    {
        return $this->belongsTo(FormPkDiriSendiriModel::class, 'fk_t_form_pk_diri_sendiri', 'form_pk_diri_sendiri_id');
    }
    public function PkOrangLain()
    {
        return $this->belongsTo(FormPkOrangLainModel::class, 'fk_t_form_pk_orang_lain', 'form_pk_orang_lain_id');
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
            $request->file('pk_bukti_aduan'),
            'pk_bukti_aduan'
        );

        $notifMessage = '';
        try {
            $data = $request->t_pernyataan_keberatan;
            $kategoriPemohon = $data['pk_kategori_pemohon'];
            $userLevel = Auth::user()->level->level_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            if ($userLevel === 'ADM') {
                $data['pk_bukti_aduan'] = $buktiAduanFile;
            }

            switch ($kategoriPemohon) {

                case 'Diri Sendiri':
                    $child = FormPkDiriSendiriModel::createData($request);
                    break;

                case 'Orang Lain':
                    $child = FormPkOrangLainModel::createData($request);
                    break;
            }

            DB::beginTransaction();

            $data['pk_kategori_pemohon'] = $kategoriPemohon;
            $data['pk_kategori_aduan'] = $kategoriAduan;
            $data['pk_bukti_aduan'] = $buktiAduanFile;
            $data['pk_status'] = 'Masuk';

            $data[$child['pkField']] = $child['id'];
            $saveData = self::create($data);
            $notifMessage = $child['message'];
            $pernyataanKeberatanId = $saveData->pernyataan_keberatan_id;

            // Create notifications dengan pernyataan_keberatani_id
            NotifAdminModel::createData($pernyataanKeberatanId, $notifMessage);
            NotifVerifikatorModel::createData($pernyataanKeberatanId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->pernyataan_keberatan_id,
                $saveData->pk_alasan_pengajuan_keberatan
            );

            $result = self::responFormatSukses($saveData, 'Pernyataan Keberatan berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Pernyataan Keberatan');
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
            't_pernyataan_keberatan.pk_kategori_pemohon' => 'required',
            't_pernyataan_keberatan.pk_alasan_pengajuan_keberatan' => 'required',
            't_pernyataan_keberatan.pk_kasus_posisi' => 'required',
        ];

        // message validasi dasar
        $message = [
            't_pernyataan_keberatan.pk_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            't_pernyataan_keberatan.pk_alasan_pengajuan_keberatan.required' => 'Alasan pengajuan keberatan wajib diisi',
            't_pernyataan_keberatan.pk_kasus_posisi.required' => 'Kasus Posisi wajib diisi',
        ];

        // Tambahkan validasi untuk admin jika diperlukan
        if (Auth::user()->level->level_kode === 'ADM') {
            $rules['pk_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['pk_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pk_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pk_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pk_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Validasi berdasarkan kategori pemohon
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi detail berdasarkan kategori pemohon
        $kategoriPemohon = $request->t_pernyataan_keberatan['pk_kategori_pemohon'];
        switch ($kategoriPemohon) {
            case 'Diri Sendiri':
                FormPkDiriSendiriModel::validasiData($request);
                break;
            case 'Orang Lain':
                FormPkOrangLainModel::validasiData($request);
                break;
        }

        return true;
    }

    public static function getTimeline()
    {
        // Ambil ID kategori form untuk 'Pernyataan Keberatan'
        $kategoriForm = KategoriFormModel::where('kf_nama', 'Pernyataan Keberatan')
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
        // Ambil ID kategori form untuk 'Pernyataan Keberatan'
        $kategoriForm = KategoriFormModel::where('kf_nama', 'Pernyataan Keberatan')
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
