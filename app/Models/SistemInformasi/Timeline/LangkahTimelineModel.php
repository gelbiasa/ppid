<?php

namespace App\Models\SistemInformasi\Timeline;

use App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LangkahTimelineModel extends Model
{
    use TraitsModel;

    protected $table = 't_langkah_timeline';
    protected $primaryKey = 'langkah_timeline_id';
    protected $fillable = [
        'fk_t_timeline',
        'langkah_timeline',
    ];

    public function Timeline()
    {
        return $this->belongsTo(TimelineModel::class, 'fk_t_timeline', 'timeline_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($timelineId, $request, $jumlahLangkah)
    {
        $langkahCreated = [];
        
        for ($i = 1; $i <= $jumlahLangkah; $i++) {
            $langkahKey = "langkah_timeline_$i";
            if (!empty($request->$langkahKey)) {
                $langkahData = [
                    'fk_t_timeline' => $timelineId,
                    'langkah_timeline' => $request->$langkahKey,
                ];
                $langkah = self::create($langkahData);
                $langkahCreated[] = $langkah;
            }
        }
        
        return $langkahCreated;
    }

    public static function updateData($timelineId, $request, $jumlahLangkah)
    {
        $existingLangkah = self::where('fk_t_timeline', $timelineId)
                              ->where('isDeleted', 0)
                              ->orderBy('langkah_timeline_id')
                              ->get();
        
        $updatedLangkah = [];
        $anyChanges = false;
        
        $deletedIndices = [];
        if ($request->has('deleted_indices')) {
            $deletedIndices = json_decode($request->deleted_indices, true) ?? [];
        }
        
        foreach ($existingLangkah as $index => $langkah) {
            $originalIndex = $index + 1;
            
            $isDeleted = in_array($originalIndex, $deletedIndices) || 
                        $request->has("deleted_step_$originalIndex");
            
            if ($isDeleted) {
                $langkah->delete();
                $anyChanges = true;
            } else {
                $langkahKey = "langkah_timeline_$originalIndex";
                
                if ($request->has($langkahKey) && $langkah->langkah_timeline !== $request->$langkahKey) {
                    $langkah->langkah_timeline = $request->$langkahKey;
                    $langkah->save();
                    $anyChanges = true;
                }
                
                $updatedLangkah[] = $langkah;
            }
        }
        
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^langkah_timeline_(\d+)$/', $key, $matches)) {
                $stepIndex = (int)$matches[1];
                
                if (in_array($stepIndex, $deletedIndices) || 
                    $request->has("deleted_step_$stepIndex") || 
                    $stepIndex <= count($existingLangkah)) {
                    continue;
                }
                
                if (!empty($value)) {
                    $langkahData = [
                        'fk_t_timeline' => $timelineId,
                        'langkah_timeline' => $value,
                    ];
                    $langkah = self::create($langkahData);
                    $updatedLangkah[] = $langkah;
                    $anyChanges = true;
                }
            }
        }
        
        return $updatedLangkah;
    }
    
    public static function deleteData($timelineId)
    {
        $langkahs = self::where('fk_t_timeline', $timelineId)
                        ->where('isDeleted', 0)
                        ->get();
                        
        foreach ($langkahs as $langkah) {
            $langkah->delete();
        }
        
        return true;
    }

    public static function validasiData($request)
    {
        $rules = [
            'jumlah_langkah_timeline' => 'required|integer|min:1|max:20',
        ];

        $messages = [
            'jumlah_langkah_timeline.required' => 'Jumlah langkah timeline wajib diisi',
            'jumlah_langkah_timeline.integer' => 'Jumlah langkah timeline harus berupa angka',
            'jumlah_langkah_timeline.min' => 'Minimal 1 langkah timeline',
            'jumlah_langkah_timeline.max' => 'Maksimal 20 langkah timeline',
        ];

        $deletedIndices = [];
        if ($request->has('deleted_indices')) {
            $deletedIndices = json_decode($request->deleted_indices, true) ?? [];
        }
        
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^deleted_step_(\d+)$/', $key, $matches)) {
                $deletedIndices[] = (int)$matches[1];
            }
        }

        $jumlahLangkah = $request->jumlah_langkah_timeline;
        if (is_numeric($jumlahLangkah)) {
            for ($i = 1; $i <= $jumlahLangkah; $i++) {
                $langkahKey = "langkah_timeline_$i";
                
                if (!in_array($i, $deletedIndices) && !$request->has("deleted_step_$i")) {
                    $rules[$langkahKey] = 'required|max:255';
                    $messages[$langkahKey.'.required'] = "Langkah timeline ke-$i wajib diisi";
                    $messages[$langkahKey.'.max'] = "Langkah timeline ke-$i maksimal 255 karakter";
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}