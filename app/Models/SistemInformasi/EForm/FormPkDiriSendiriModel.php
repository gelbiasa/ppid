<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormPkDiriSendiriModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pk_diri_sendiri';
    protected $primaryKey = 'form_pk_diri_sendiri_id';
    protected $fillable = [
        'pk_nama_pengguna',
        'pk_alamat_pengguna',
        'pk_pekerjaan_pengguna',
        'pk_no_hp_pengguna',
        'pk_email_pengguna',
        'pk_upload_nik_pengguna',
    ];

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
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('pk_upload_nik_pengguna'),
            'pk_identitas_pelapor_ds'
        );
        
        try {
            $data = $request->t_form_pk_diri_sendiri;

            $userLevel = Auth::user()->level->level_kode;
            if ($userLevel === 'RPN') {
                $data['pk_nama_pengguna'] = Auth::user()->nama_pengguna;
                $data['pk_alamat_pengguna'] = Auth::user()->alamat_pengguna;
                $data['pk_pekerjaan_pengguna'] = Auth::user()->pekerjaan_pengguna;
                $data['pk_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pk_email_pengguna'] = Auth::user()->email_pengguna;
                $data['pk_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pk_upload_nik_pengguna'] = $uploadNikPelaporFile;
            }
            $saveData = self::create($data);

            $result = [
                'pkField' => 'fk_t_form_pk_diri_sendiri', // Perbaikan nama field relasi
                'id' => $saveData->form_pk_diri_sendiri_id,
                'message' => "{$saveData->pk_nama_pengguna} Mengajukan Pernyataan Keberatan",
            ];
            return $result;
        } catch (\Exception $e) {
            self::removeFile($uploadNikPelaporFile);
            throw $e;
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
        // Cek apakah validasi untuk admin diperlukan
        if (Auth::user()->level->level_kode === 'ADM') {
            // rules validasi untuk form diri sendiri
            $rules = [
                't_form_pk_diri_sendiri.pk_nama_pengguna' => 'required',
                't_form_pk_diri_sendiri.pk_alamat_pengguna' => 'required',
                't_form_pk_diri_sendiri.pk_pekerjaan_pengguna' => 'required', 
                't_form_pk_diri_sendiri.pk_no_hp_pengguna' => 'required',
                't_form_pk_diri_sendiri.pk_email_pengguna' => 'required|email',
                'pk_upload_nik_pengguna' => 'required|image|max:10240',
            ];

            // message validasi
            $message = [
                't_form_pk_diri_sendiri.pk_nama_pengguna.required' => 'Nama pengguna wajib diisi',
                't_form_pk_diri_sendiri.pk_alamat_pengguna.required' => 'Alamat pengguna wajib diisi',
                't_form_pk_diri_sendiri.pk_pekerjaan_pengguna.required' => 'Pekerjaan pengguna wajib diisi', 
                't_form_pk_diri_sendiri.pk_no_hp_pengguna.required' => 'Nomor HP pengguna wajib diisi',
                't_form_pk_diri_sendiri.pk_email_pengguna.required' => 'Email pengguna wajib diisi',
                't_form_pk_diri_sendiri.pk_email_pengguna.email' => 'Format email tidak valid',
                'pk_upload_nik_pengguna.required' => 'Upload NIK pengguna wajib diisi',
                'pk_upload_nik_pengguna.image' => 'File harus berupa gambar',
                'pk_upload_nik_pengguna.max' => 'Ukuran file tidak boleh lebih dari 10MB',
            ];

            // Lakukan validasi
            $validator = Validator::make($request->all(), $rules, $message);

            // Lemparkan exception jika validasi gagal
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }
}
