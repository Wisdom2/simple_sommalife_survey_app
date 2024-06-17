<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class QuestionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'type' => 'text',
                'display_name' => 'Text'
            ],
            [
                'type' => 'radio',
                'display_name' => 'Single Choice'
            ],
            [
                'type' => 'checkbox',
                'display_name' => 'Multiple Choice'
            ],
            [
                'type' => 'image',
                'display_name' => 'Image'
            ],
        ];

        foreach ($types as $type) {
            QuestionType::firstOrCreate(['type' => $type['type']], $type);
        }
    }
}
