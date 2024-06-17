<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Dyrynda\Database\Casts\EfficientUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = ['questionnaire_id', 'question_type_id', 'question'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid' => EfficientUuid::class,
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function questiontype()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, );
    }
    
}
