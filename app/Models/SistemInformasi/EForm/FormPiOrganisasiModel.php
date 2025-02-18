<?php

namespace App\Models\SistemInformasi\EForm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormPiOrganisasiModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_form_pi_organisasi';
    protected $primaryKey = 'form_pi_organisasi_id';
    protected $fillable = [
        'pi_nama_organisasi',
        'pi_no_telp_organisasi',
        'pi_email_atau_medsos_organisasi',
        'pi_nama_narahubung',
        'pi_no_telp_narahubung',
        'pi_identitas_narahubung',
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
            $request->file('pi_identitas_narahubung'),
            'pi_organisasi_identitas'
        );

        $organisasi = FormPiOrganisasiModel::create([
            'pi_nama_organisasi' => $request->pi_nama_organisasi,
            'pi_no_telp_organisasi' => $request->pi_no_telp_organisasi,
            'pi_email_atau_medsos_organisasi' => $request->pi_email_atau_medsos_organisasi,
            'pi_nama_narahubung' => $request->pi_nama_narahubung,
            'pi_no_telp_narahubung' => $request->pi_no_telp_narahubung,
            'pi_identitas_narahubung' => $fileName,
            'created_by' => session('alias')
        ]);

        return [
            ['fk_t_form_pi_organisasi' => $organisasi->form_pi_organisasi_id],
            $request->pi_nama_organisasi . ' Mengajukan Permohonan Informasi'
        ];
    }

    public static function validasiData($request)
    {
        $validator = Validator::make($request->all(), [
            'pi_identitas_narahubung' => 'required|image|max:10240',
            'pi_nama_organisasi' => 'required',
            'pi_no_telp_organisasi' => 'required',
            'pi_email_atau_medsos_organisasi' => 'required',
            'pi_nama_narahubung' => 'required',
            'pi_no_telp_narahubung' => 'required',
        ], [
            'pi_identitas_narahubung.required' => 'Identitas narahubung wajib diisi',
            'pi_identitas_narahubung.image' => 'File harus berupa gambar',
            'pi_identitas_narahubung.max' => 'Ukuran file tidak boleh lebih dari 10MB',
            'pi_nama_organisasi.required' => 'Nama organisasi wajib diisi',
            'pi_no_telp_organisasi.required' => 'Nomor telepon organisasi wajib diisi',
            'pi_email_atau_medsos_organisasi.required' => 'Email atau media sosial organisasi wajib diisi',
            'pi_nama_narahubung.required' => 'Nama narahubung wajib diisi',
            'pi_no_telp_narahubung.required' => 'Nomor telepon narahubung wajib diisi',
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
