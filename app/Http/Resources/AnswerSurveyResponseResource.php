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
        $response = $this->responses()->first();

        $data['answer'] = [
            'id' => $this->uuid,
            'answer' => $this->answer,
        ];
    
        if (!blank($response)) {
            $data['response'] = [
                'id' => $response->uuid,
                'value' => $response->answer->answer ?? ''
            ];
        }
    
        return $data;
    }
}
