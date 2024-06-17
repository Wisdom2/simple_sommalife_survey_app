<?php

namespace App\Http\Requests;

use App\Models\QuestionType;
use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Dyrynda\Database\Rules\EfficientUuidExists;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string'],
            'questionnaire_id' => ['required', new EfficientUuidExists(Questionnaire::class)],
            'question_type_id' => ['required', new EfficientUuidExists(QuestionType::class)],
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'status' => 422,
                'errors' => $errors,
                'message' => 'The given data is invalid',
            ], 422)
        );
    }
}
