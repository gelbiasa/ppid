<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormPiDiriSendiriModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pi_diri_sendiri';
    protected $primaryKey = 'form_pi_diri_sendiri_id';
    protected $fillable = [
        'pi_nama_pengguna',
        'pi_alamat_pengguna',
        'pi_no_hp_pengguna',
        'pi_email_pengguna',
        'pi_upload_nik_pengguna'
    ];

    // Konstruktor untuk menggabungkan field umum
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
            $request->file('pi_upload_nik_pengguna'),
            'pi_identitas_pelapor_ds'
        );

        try {
            $data = $request->t_form_pi_diri_sendiri;

            $userLevel = Auth::user()->level->hak_akses_kode;
            if ($userLevel === 'RPN') {
                $data['pi_nama_pengguna'] = Auth::user()->nama_pengguna;
                $data['pi_alamat_pengguna'] = Auth::user()->alamat_pengguna;
                $data['pi_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pi_email_pengguna'] = Auth::user()->email_pengguna;
                $data['pi_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pi_upload_nik_pengguna'] = $uploadNikPelaporFile;
            }
            $saveData = self::create($data);

            $result = [
                'pkField' => 'fk_t_form_pi_diri_sendiri', // Perbaikan nama field relasi
                'id' => $saveData->form_pi_diri_sendiri_id,
                'message' => "{$saveData->pi_nama_pengguna} Mengajukan Permohonan Informasi",
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
        if (Auth::user()->level->hak_akses_kode === 'ADM') {
            // rules validasi untuk form diri sendiri
            $rules = [
                't_form_pi_diri_sendiri.pi_nama_pengguna' => 'required',
                't_form_pi_diri_sendiri.pi_alamat_pengguna' => 'required',
                't_form_pi_diri_sendiri.pi_no_hp_pengguna' => 'required',
                't_form_pi_diri_sendiri.pi_email_pengguna' => 'required|email',
                'pi_upload_nik_pengguna' => 'required|image|max:10240',
            ];

            // message validasi
            $message = [
                't_form_pi_diri_sendiri.pi_nama_pengguna.required' => 'Nama pengguna wajib diisi',
                't_form_pi_diri_sendiri.pi_alamat_pengguna.required' => 'Alamat pengguna wajib diisi',
                't_form_pi_diri_sendiri.pi_no_hp_pengguna.required' => 'Nomor HP pengguna wajib diisi',
                't_form_pi_diri_sendiri.pi_email_pengguna.required' => 'Email pengguna wajib diisi',
                't_form_pi_diri_sendiri.pi_email_pengguna.email' => 'Format email tidak valid',
                'pi_upload_nik_pengguna.required' => 'Upload NIK pengguna wajib diisi',
                'pi_upload_nik_pengguna.image' => 'File harus berupa gambar',
                'pi_upload_nik_pengguna.max' => 'Ukuran file tidak boleh lebih dari 10MB',
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
