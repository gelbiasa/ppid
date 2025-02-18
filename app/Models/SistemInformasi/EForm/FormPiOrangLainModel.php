<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class FormPiOrangLainModel extends Model
{
    use HasFactory, SoftDeletes;

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
        'pi_upload_nik_pengguna_informasi',
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public static function createData($request)
    {
        $fileName = self::uploadFile(
            $request->file('pi_upload_nik_pengguna_informasi'),
            'pi_ol_upload_nik'
        );

        $orangLain = FormPiOrangLainModel::create([
            'pi_nama_pengguna_penginput' => Auth::user()->nama_pengguna,
            'pi_alamat_pengguna_penginput' => Auth::user()->alamat_pengguna,
            'pi_no_hp_pengguna_penginput' => Auth::user()->no_hp_pengguna,
            'pi_email_pengguna_penginput' => Auth::user()->email_pengguna,
            'pi_upload_nik_pengguna_penginput' => Auth::user()->upload_nik_pengguna,
            'pi_nama_pengguna_informasi' => $request->pi_nama_pengguna_informasi,
            'pi_alamat_pengguna_informasi' => $request->pi_alamat_pengguna_informasi,
            'pi_no_hp_pengguna_informasi' => $request->pi_no_hp_pengguna_informasi,
            'pi_email_pengguna_informasi' => $request->pi_email_pengguna_informasi,
            'pi_upload_nik_pengguna_informasi' => $fileName,
            'created_by' => session('alias')
        ]);

        return [
            ['fk_t_form_pi_orang_lain' => $orangLain->form_pi_orang_lain_id],
            $request->pi_nama_pengguna_informasi . ' Mengajukan Permohonan Informasi'
        ];
    }

    public static function validasiData($request)
    {
        $validator = Validator::make($request->all(), [
            'pi_upload_nik_pengguna_informasi' => 'required|image|max:10240',
            'pi_nama_pengguna_informasi' => 'required',
            'pi_alamat_pengguna_informasi' => 'required',
            'pi_no_hp_pengguna_informasi' => 'required',
            'pi_email_pengguna_informasi' => 'required|email',
        ], [
            'pi_upload_nik_pengguna_informasi.required' => 'Upload NIK wajib diisi',
            'pi_upload_nik_pengguna_informasi.image' => 'File harus berupa gambar',
            'pi_upload_nik_pengguna_informasi.max' => 'Ukuran file tidak boleh lebih dari 10MB',
            'pi_nama_pengguna_informasi.required' => 'Nama pengguna informasi wajib diisi',
            'pi_alamat_pengguna_informasi.required' => 'Alamat pengguna informasi wajib diisi',
            'pi_no_hp_pengguna_informasi.required' => 'Nomor HP pengguna informasi wajib diisi',
            'pi_email_pengguna_informasi.required' => 'Email pengguna informasi wajib diisi',
            'pi_email_pengguna_informasi.email' => 'Format email tidak valid',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private static function uploadFile($file, $prefix)
    {
        $fileName = $prefix . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        return $fileName;
    }
}
