<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->efficientUuid('uuid')->unique();
            $table->unsignedBigInteger('questionnaire_id');
            $table->unsignedBigInteger('question_type_id');
            $table->string('question');
            $table->foreign('questionnaire_id')->references('id')->on('questionnaires');
            $table->foreign('question_type_id')->references('id')->on('question_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
