<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\QuestionnaireRequest;
use App\Http\Resources\QuestionnaireResource;
use App\Http\Resources\QuestionnaireWithQuestionResource;
use App\Http\Resources\QuestionnaireWithQuestionAnswerSurveyResponseResource;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = (blank(request()->limit)) ? 15 : request()->limit;

        $questionnaires = auth()->user()->questionnaires()->latest()->paginate($limit);

        return response()->json([
            'status' => 200,
            'data' => QuestionnaireResource::collection($questionnaires),
            'pagination' => paginatedResponse($questionnaires),
            'message' => 'Questionnaire(s) retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionnaireRequest $request)
    {
        $this->ensureUserQuestionnaireHasUniqueTitle();

        $questionnaire = auth()->user()->questionnaires()->create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => 200,
            'data' => new QuestionnaireResource($questionnaire),
            'message' => 'Questionnaire created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $questionnaire = Questionnaire::whereUuid($id)->with(['questions.answers'])->first();

        throw_if(
            blank( $questionnaire ),
             new CustomException(message: 'Record not found', status: 404)
         );


        return response()->json([
            'status' => 200,
            'data' => new QuestionnaireWithQuestionAnswerSurveyResponseResource($questionnaire),
            'message' => 'Questionnaire created successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionnaireRequest $request, string $id)
    {

        $questionnaire = Questionnaire::whereUuid($id)->first();

        throw_if(
            blank( $questionnaire ),
             new CustomException(message: 'Record not found', status: 404)
         );

         throw_if(
           $questionnaire->user_id != auth()->id() ,
             new CustomException(message: 'Can only modify own record', status: 403)
         );


        auth()->user()->questionnaires()->whereUuid($id)->update([
            'title' => $request->title,
            'description' => $request->description
        ]);


        return response()->json([
            'status' => 200,
            'data' => new QuestionnaireResource( $questionnaire->refresh() ),
            'message' => 'Questionnaire updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyQuestion(string $questionnaireId, string $questionId)
    {
        $questionnaire = Questionnaire::whereUuid($questionnaireId)->where('user_id', auth()->id())->first();

        throw_if(
            blank( $questionnaire ),
             new CustomException(message: 'Record not found', status: 404)
         );

         $question = Question::whereUuid($questionId)->with('answers')->first();

         throw_if(
            blank( $question ),
             new CustomException(message: 'Question not found', status: 404)
         );


         $surveyResponse = SurveyResponse::where('question_id', $question->id)->first();

         throw_if(
           ! blank( $surveyResponse ),
             new CustomException(message: 'Cannot delete question which has been surveyed', status: 422)
         );

         DB::beginTransaction();

         try {

            $question->answers()->delete();
            $question->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'data' => new QuestionnaireWithQuestionResource( $questionnaire->refresh() ),
                'message' => 'Question deleted successfully',
            ]);

         } catch (\Exception $ex) {
            DB::rollBack();

            Log::error($ex->getMessage());

            return response()->json([
                'status' => 400,
                'data' => [],
                'message' => 'Failed to delete question',
            ]);

         }


    }

    private function ensureUserQuestionnaireHasUniqueTitle()
    {
        $questionnaire = auth()->user()->questionnaires()->where('title', request('title'))->first();

        throw_if(
           ! blank( $questionnaire ),
            new CustomException(message: 'Title already exist for user', status: 409)
        );
    }
}
