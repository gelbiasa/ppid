<?php

namespace App\Models\SistemInformasi\EForm;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormPiOrganisasiModel extends BaseModel
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
        'pi_identitas_narahubung'
    ];

    // Konstruktor untuk menggabungkan field umum
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        $fileName = self::uploadFile(
            $request->file('pi_identitas_narahubung'),
            'pi_organisasi_identitas'
        );

        try {
            $data = $request->t_form_pi_organisasi;
            $data['pi_identitas_narahubung'] = $fileName;
            $saveData = self::create($data);

            $result = [
                'pkField' => 'fk_t_form_pi_organisasi', // Perbaikan nama field relasi
                'id' => $saveData->form_pi_organisasi_id,
                'message' => "{$saveData->pi_nama_organisasi} Mengajukan Permohonan Informasi",
            ];
            return $result;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, hapus file yang sudah diupload
            self::removeFile($fileName);
            throw $e;
        }
    }

    public static function validasiData($request)
    {
        $validator = Validator::make($request->all(), [
            't_form_pi_organisasi.pi_nama_organisasi' => 'required',
            't_form_pi_organisasi.pi_no_telp_organisasi' => 'required',
            't_form_pi_organisasi.pi_email_atau_medsos_organisasi' => 'required',
            't_form_pi_organisasi.pi_nama_narahubung' => 'required',
            't_form_pi_organisasi.pi_no_telp_narahubung' => 'required',
            'pi_identitas_narahubung' => 'required|image|max:10240',
        ], [
            't_form_pi_organisasi.pi_nama_organisasi.required' => 'Nama organisasi wajib diisi',
            't_form_pi_organisasi.pi_no_telp_organisasi.required' => 'Nomor telepon organisasi wajib diisi',
            't_form_pi_organisasi.pi_email_atau_medsos_organisasi.required' => 'Email atau media sosial organisasi wajib diisi',
            't_form_pi_organisasi.pi_nama_narahubung.required' => 'Nama narahubung wajib diisi',
            't_form_pi_organisasi.pi_no_telp_narahubung.required' => 'Nomor telepon narahubung wajib diisi',
            'pi_identitas_narahubung.required' => 'Identitas narahubung wajib diisi',
            'pi_identitas_narahubung.image' => 'File harus berupa gambar',
            'pi_identitas_narahubung.max' => 'Ukuran file tidak boleh lebih dari 10MB',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
