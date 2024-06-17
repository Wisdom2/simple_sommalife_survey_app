<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\QuestionTypeTrait;
use App\Http\Resources\QuestionResource;
use App\Http\Requests\CreateQuestionRequest;

class QuestionController extends Controller
{
    use QuestionTypeTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateQuestionRequest $request)
    {
        DB::beginTransaction();

        $questionnaire = Questionnaire::whereUuid($request->questionnaire_id)->first();

        $questionType = QuestionType::whereUuid($request->question_type_id)->first();

        $this->ensureValidQuestionTypeformat($questionType);

        try {
          
            $question = $questionnaire->questions()->create([
                'question' => $request->question,
                'question_type_id' =>  $questionType->id
            ]);

            $this->attachAnswersToQuestion($question);

            DB::commit();

            return response()->json([
                'status' => 200,
                'data' => new QuestionResource($question),
                'message' => 'Question created successfully',
            ]);
        } catch (\Exception $ex) {

            DB::rollBack();

            Log::error($ex->getMessage());

            return response()->json([
                'status' => 400,
                'data' => [],
                'message' => 'Failed to create question',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }

    private function attachAnswersToQuestion(Question $question)
    {
        if( in_array($question->questiontype->type, ['radio', 'checkbox'] ) ) {

            foreach( request('answers') as $answer ) {

                $question->answers()->create([
                    'answer' => $answer,
                ]);

            }
        
        } else {

            $question->answers()->create([
                'answer' => '',
            ]);

        }

    }

   
}
