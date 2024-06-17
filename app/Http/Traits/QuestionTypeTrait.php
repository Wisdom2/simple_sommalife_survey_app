<?php
namespace App\Http\Traits;

use App\Models\QuestionType;
use App\Exceptions\CustomException;

trait QuestionTypeTrait
{
    private function ensureValidQuestionTypeformat(QuestionType $questionType)
    {
        if( in_array($questionType->type, ['radio', 'checkbox'] ) ) {

            throw_if(
               ! is_array( request('answers') ) || blank(  request('answers') ),
                 new CustomException(message: 'Single Choice & Multiple Choice must be in array format', status: 400)
             );

        }

    }
}
