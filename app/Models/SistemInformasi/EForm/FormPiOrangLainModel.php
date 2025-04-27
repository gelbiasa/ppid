<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormPiOrangLainModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pi_orang_lain';
    protected $primaryKey = 'form_pi_orang_lain_id';
    protected $fillable = [
        'pi_nama_pengguna_penginput',
        'pi_alamat_pengguna_penginput',
        'pi_no_hp_pengguna_penginput',
        'pi_email_pengguna_penginput',
        'pi_upload_nik_pengguna_penginput',
        'pi_nama_pengguna_informasi',
        'pi_alamat_pengguna_informasi',
        'pi_no_hp_pengguna_informasi',
        'pi_email_pengguna_informasi',
        'pi_upload_nik_pengguna_informasi'
    ];

    // Konstruktor untuk menggabungkan field umum
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        // Upload file untuk pengguna informasi
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('pi_upload_nik_pengguna_penginput'),
            'pi_identitas_pelapor_ol'
        );

        $uploadNikFile = self::uploadFile(
            $request->file('pi_upload_nik_pengguna_informasi'),
            'pi_ol_upload_nik'
        );

        try {
            $data = $request->t_form_pi_orang_lain;

            $userLevel = Auth::user()->level->hak_akses_kode;

            if ($userLevel === 'RPN') {
                $data['pi_nama_pengguna_penginput'] = Auth::user()->nama_pengguna;
                $data['pi_alamat_pengguna_penginput'] = Auth::user()->alamat_pengguna;
                $data['pi_no_hp_pengguna_penginput'] = Auth::user()->no_hp_pengguna;
                $data['pi_email_pengguna_penginput'] = Auth::user()->email_pengguna;
                $data['pi_upload_nik_pengguna_penginput'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pi_upload_nik_pengguna_penginput'] = $uploadNikPelaporFile;
            }
            $data['pi_upload_nik_pengguna_informasi'] = $uploadNikFile;

            $saveData = self::create($data);

            $result = [
                'pkField' => 'fk_t_form_pi_orang_lain', // Perbaikan nama field relasi
                'id' => $saveData->form_pi_orang_lain_id,
                'message' => "{$saveData->pi_nama_pengguna_informasi} Mengajukan Permohonan Informasi",
            ];
            return $result;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, hapus file yang sudah diupload
            self::removeFile($uploadNikFile);
            self::removeFile($uploadNikPelaporFile);
            throw $e;
        }
    }

    public static function validasiData($request)
    {
        // Inisialisasi rules dan messages
        $rules = [];
        $message = [];

        // Jika user adalah ADM, tambahkan validasi untuk penginput
        if (Auth::user()->level->hak_akses_kode === 'ADM') {
            $rules = array_merge($rules, [
                't_form_pi_orang_lain.pi_nama_pengguna_penginput' => 'required',
                't_form_pi_orang_lain.pi_alamat_pengguna_penginput' => 'required',
                't_form_pi_orang_lain.pi_no_hp_pengguna_penginput' => 'required',
                't_form_pi_orang_lain.pi_email_pengguna_penginput' => 'required|email',
                'pi_upload_nik_pengguna_penginput' => 'required|image|max:10240',
            ]);

            $message = array_merge($message, [
                't_form_pi_orang_lain.pi_nama_pengguna_penginput.required' => 'Nama penginput wajib diisi',
                't_form_pi_orang_lain.pi_alamat_pengguna_penginput.required' => 'Alamat penginput wajib diisi',
                't_form_pi_orang_lain.pi_no_hp_pengguna_penginput.required' => 'Nomor HP penginput wajib diisi',
                't_form_pi_orang_lain.pi_email_pengguna_penginput.required' => 'Email penginput wajib diisi',
                't_form_pi_orang_lain.pi_email_pengguna_penginput.email' => 'Format email penginput tidak valid',
                'pi_upload_nik_pengguna_penginput.required' => 'Upload NIK penginput wajib diisi',
                'pi_upload_nik_pengguna_penginput.image' => 'File NIK penginput harus berupa gambar',
                'pi_upload_nik_pengguna_penginput.max' => 'Ukuran file NIK penginput tidak boleh lebih dari 10MB',
            ]);
        }

        // Tambahkan rules untuk pengguna informasi (berlaku untuk semua level)
        $rules = array_merge($rules, [
            't_form_pi_orang_lain.pi_nama_pengguna_informasi' => 'required',
            't_form_pi_orang_lain.pi_alamat_pengguna_informasi' => 'required',
            't_form_pi_orang_lain.pi_no_hp_pengguna_informasi' => 'required',
            't_form_pi_orang_lain.pi_email_pengguna_informasi' => 'required|email',
            'pi_upload_nik_pengguna_informasi' => 'required|image|max:10240',
        ]);

        $message = array_merge($message, [
            't_form_pi_orang_lain.pi_nama_pengguna_informasi.required' => 'Nama pengguna informasi wajib diisi',
            't_form_pi_orang_lain.pi_alamat_pengguna_informasi.required' => 'Alamat pengguna informasi wajib diisi',
            't_form_pi_orang_lain.pi_no_hp_pengguna_informasi.required' => 'Nomor HP pengguna informasi wajib diisi',
            't_form_pi_orang_lain.pi_email_pengguna_informasi.required' => 'Email pengguna informasi wajib diisi',
            't_form_pi_orang_lain.pi_email_pengguna_informasi.email' => 'Format email pengguna informasi tidak valid',
            'pi_upload_nik_pengguna_informasi.required' => 'Upload NIK pengguna informasi wajib diisi',
            'pi_upload_nik_pengguna_informasi.image' => 'File NIK pengguna informasi harus berupa gambar',
            'pi_upload_nik_pengguna_informasi.max' => 'Ukuran file NIK pengguna informasi tidak boleh lebih dari 10MB',
        ]);

        // Lakukan validasi
        $validator = Validator::make($request->all(), $rules, $message);

        // Lemparkan exception jika validasi gagal
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
