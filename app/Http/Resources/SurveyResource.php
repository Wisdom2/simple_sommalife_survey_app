<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
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
            'questionnaire' => new QuestionnaireResource( $this->questionnaire ),
            'respondent' => [
                'name' => $this->name,
                'email' => $this->email,
            ],
        ];
    }
}
