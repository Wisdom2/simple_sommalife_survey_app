<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
        if ( in_array($questiontype->type, ['radio', 'checkbox'] ) ) {

            return  AnswerResource::collection($this->answers);
        }

        return $this->answers->first()->uuid;
       
    }
}
