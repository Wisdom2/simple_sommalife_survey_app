<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyResponse extends Model
{
    use HasFactory, GeneratesUuid; 

    protected $fillable = ['survey_id',  'question_id', 'answer_id', 'value'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
}
