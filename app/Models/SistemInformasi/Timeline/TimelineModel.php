<?php

namespace App\Models\SistemInformasi\Timeline;

use App\Models\Log\TransactionModel;
use App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TimelineModel extends Model
{
    use TraitsModel;

    protected $table = 't_timeline';
    protected $primaryKey = 'timeline_id';
    protected $fillable = [
        'fk_m_kategori_form',
        'judul_timeline'
    ];

    public function TimelineKategoriForm()
    {
        return $this->belongsTo(KategoriFormModel::class, 'fk_m_kategori_form', 'kategori_form_id');
    }

    public function langkahTimeline()
    {
        return $this->hasMany(LangkahTimelineModel::class, 'fk_t_timeline', 'timeline_id')
            ->where('isDeleted', 0);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->with('TimelineKategoriForm')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_timeline', 'like', "%{$search}%")
                    ->orWhereHas('TimelineKategoriForm', function ($subq) use ($search) {
                        $subq->where('kf_nama', 'like', "%{$search}%");
                    });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->t_timeline;
            $timeline = self::create($data);

            $jumlahLangkah = $request->jumlah_langkah_timeline;
            LangkahTimelineModel::createData($timeline->timeline_id, $request, $jumlahLangkah);

            TransactionModel::createData(
                'CREATED',
                $timeline->timeline_id, // ID aktivitas adalah ID timeline yang baru dibuat
                $timeline->judul_timeline // Detail aktivitas adalah judul timeline
            );

            DB::commit();

            return self::responFormatSukses($timeline, 'Timeline berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat timeline');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $timeline = self::findOrFail($id);

            $data = $request->t_timeline;
            $timeline->update($data);

            $jumlahLangkah = $request->jumlah_langkah_timeline;
            LangkahTimelineModel::updateData($timeline->timeline_id, $request, jumlahLangkah: $jumlahLangkah);

            TransactionModel::createData(
                'UPDATED',
                $timeline->timeline_id,
                $timeline->judul_timeline
            );

            DB::commit();

            return self::responFormatSukses($timeline, 'Timeline berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui timeline');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $timeline = self::findOrFail($id);

            $timeline->delete();

            LangkahTimelineModel::deleteData($id);

            TransactionModel::createData(
                'DELETED',
                $timeline->timeline_id, // ID aktivitas adalah ID timeline yang baru dibuat
                $timeline->judul_timeline // Detail aktivitas adalah judul timeline
            );

            DB::commit();

            return self::responFormatSukses($timeline, 'Timeline berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus timeline');
        }
    }

    public static function detailData($id)
    {
        return self::with(['langkahTimeline', 'TimelineKategoriForm'])->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_timeline.fk_m_kategori_form' => 'required|exists:m_kategori_form,kategori_form_id',
            't_timeline.judul_timeline' => 'required|max:255',
        ];

        $messages = [
            't_timeline.fk_m_kategori_form.required' => 'Kategori form wajib dipilih',
            't_timeline.fk_m_kategori_form.exists' => 'Kategori form tidak valid',
            't_timeline.judul_timeline.required' => 'Judul timeline wajib diisi',
            't_timeline.judul_timeline.max' => 'Judul timeline maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        LangkahTimelineModel::validasiData($request);

        return true;
    }
}
