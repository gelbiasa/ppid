<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormPkOrangLainModel extends Model
{
    use TraitsModel;

    protected $table = 't_form_pk_orang_lain';
    protected $primaryKey = 'form_pk_orang_lain_id';
    protected $fillable = [
        'pk_nama_pengguna_penginput',
        'pk_alamat_pengguna_penginput',
        'pk_pekerjaan_pengguna_penginput',
        'pk_no_hp_pengguna_penginput',
        'pk_email_pengguna_penginput',
        'pk_upload_nik_pengguna_penginput',
        'pk_nama_kuasa_pemohon',
        'pk_alamat_kuasa_pemohon',
        'pk_no_hp_kuasa_pemohon',
        'pk_email_kuasa_pemohon',
        'pk_upload_nik_kuasa_pemohon'
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
        // Upload file untuk pengguna informasi
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('pk_upload_nik_pengguna_penginput'),
            'pk_identitas_pelapor_ol'
        );

        $uploadNikFile = self::uploadFile(
            $request->file('pk_upload_nik_kuasa_pemohon'),
            'pk_ol_upload_nik'
        );

        try {
            $data = $request->t_form_pk_orang_lain;

            $userLevel = Auth::user()->level->hak_akses_kode;

            if ($userLevel === 'RPN') {
                $data['pk_nama_pengguna_penginput'] = Auth::user()->nama_pengguna;
                $data['pk_alamat_pengguna_penginput'] = Auth::user()->alamat_pengguna;
                $data['pk_pekerjaan_pengguna_penginput'] = Auth::user()->pekerjaan_pengguna;
                $data['pk_no_hp_pengguna_penginput'] = Auth::user()->no_hp_pengguna;
                $data['pk_email_pengguna_penginput'] = Auth::user()->email_pengguna;
                $data['pk_upload_nik_pengguna_penginput'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pk_upload_nik_pengguna_penginput'] = $uploadNikPelaporFile;
            }
            $data['pk_upload_nik_kuasa_pemohon'] = $uploadNikFile;

            $saveData = self::create($data);

            $result = [
                'pkField' => 'fk_t_form_pk_orang_lain', // Perbaikan nama field relasi
                'id' => $saveData->form_pk_orang_lain_id,
                'message' => "{$saveData->pk_nama_kuasa_pemohon} Mengajukan Pernyataan Keberatan",
            ];
            return $result;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, hapus file yang sudah diupload
            self::removeFile($uploadNikFile);
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
        // Inisialisasi rules dan messages
        $rules = [];
        $message = [];

        // Jika user adalah ADM, tambahkan validasi untuk penginput
        if (Auth::user()->level->hak_akses_kode === 'ADM') {
            $rules = array_merge($rules, [
                't_form_pk_orang_lain.pk_nama_pengguna_penginput' => 'required',
                't_form_pk_orang_lain.pk_alamat_pengguna_penginput' => 'required',
                't_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput' => 'required',
                't_form_pk_orang_lain.pk_no_hp_pengguna_penginput' => 'required',
                't_form_pk_orang_lain.pk_email_pengguna_penginput' => 'required|email',
                'pk_upload_nik_pengguna_penginput' => 'required|image|max:10240',
            ]);

            $message = array_merge($message, [
                't_form_pk_orang_lain.pk_nama_pengguna_penginput.required' => 'Nama penginput wajib diisi',
                't_form_pk_orang_lain.pk_alamat_pengguna_penginput.required' => 'Alamat penginput wajib diisi',
                't_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput.required' => 'Pekerjaan penginput wajib diisi',
                't_form_pk_orang_lain.pk_no_hp_pengguna_penginput.required' => 'Nomor HP penginput wajib diisi',
                't_form_pk_orang_lain.pk_email_pengguna_penginput.required' => 'Email penginput wajib diisi',
                't_form_pk_orang_lain.pk_email_pengguna_penginput.email' => 'Format email penginput tidak valid',
                'pk_upload_nik_pengguna_penginput.required' => 'Upload NIK penginput wajib diisi',
                'pk_upload_nik_pengguna_penginput.image' => 'File NIK penginput harus berupa gambar',
                'pk_upload_nik_pengguna_penginput.max' => 'Ukuran file NIK penginput tidak boleh lebih dari 10MB',
            ]);
        }

        // Tambahkan rules untuk pengguna informasi (berlaku untuk semua level)
        $rules = array_merge($rules, [
            't_form_pk_orang_lain.pk_nama_kuasa_pemohon' => 'required',
            't_form_pk_orang_lain.pk_alamat_kuasa_pemohon' => 'required',
            't_form_pk_orang_lain.pk_no_hp_kuasa_pemohon' => 'required',
            't_form_pk_orang_lain.pk_email_kuasa_pemohon' => 'required|email',
            'pk_upload_nik_kuasa_pemohon' => 'required|image|max:10240',
        ]);

        $message = array_merge($message, [
            't_form_pk_orang_lain.pk_nama_kuasa_pemohon.required' => 'Nama pengguna informasi wajib diisi',
            't_form_pk_orang_lain.pk_alamat_kuasa_pemohon.required' => 'Alamat pengguna informasi wajib diisi',
            't_form_pk_orang_lain.pk_no_hp_kuasa_pemohon.required' => 'Nomor HP pengguna informasi wajib diisi',
            't_form_pk_orang_lain.pk_email_kuasa_pemohon.required' => 'Email pengguna informasi wajib diisi',
            't_form_pk_orang_lain.pk_email_kuasa_pemohon.email' => 'Format email pengguna informasi tidak valid',
            'pk_upload_nik_kuasa_pemohon.required' => 'Upload NIK pengguna informasi wajib diisi',
            'pk_upload_nik_kuasa_pemohon.image' => 'File NIK pengguna informasi harus berupa gambar',
            'pk_upload_nik_kuasa_pemohon.max' => 'Ukuran file NIK pengguna informasi tidak boleh lebih dari 10MB',
        ]);

        // Lakukan validasi
        $validator = Validator::make($request->all(), $rules, $message);

        // Lemparkan exception jika validasi gagal
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
