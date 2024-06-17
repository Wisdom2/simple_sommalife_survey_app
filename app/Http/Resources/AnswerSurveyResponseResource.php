<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerSurveyResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data['answer'] = [
            'id' => $this->uuid,
            'answer' => $this->answer,
        ];

        if( ! blank( $this->responses()->first()?->answer ) ) {
            $data['response'] = [
                'id' => $this->responses()->first()?->uuid,
                'value' => $this->responses()->first()?->answer->answer
            ];
        }

        return $data;
    }
}
