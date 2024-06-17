<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'question' => $this->question,
            'created_at' => $this->created_at->format('Y-m-d h:i:s a'),
            'updated_at' => $this->updated_at->format('Y-m-d h:i:s a'),
            'type' => new QuestionTypeResource($this->questiontype),
            'answers' => $this->transformAnswers($this->questiontype),
        ];
    }

    private function transformAnswers($questiontype)
    {
        if (in_array($questiontype->type, ['radio', 'checkbox'])) {
            return AnswerSurveyResponseResource::collection($this->answers);
        } elseif ($questiontype->type == 'image') {
            return [
                [
                    'id' => $this->answers->first()->uuid,
                    'value' => config('services.storage_url') . Arr::get($this->answers->first(), 'responses.0.value'),
                ]
            ];
        }

        return [
            [
                'id' => $this->answers->first()->uuid,
                'value' => Arr::get($this->answers->first(), 'responses.0.value'),
            ]
        ];
    }
 
}
