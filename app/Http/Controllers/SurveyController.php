<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Survey;
use App\Models\Question;
use Illuminate\Support\Arr;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Exceptions\CustomException;
use App\Http\Resources\SurveyResource;
use App\Http\Traits\QuestionTypeTrait;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SurveyResultResource;
use App\Http\Resources\QuestionnaireWithQuestionResource;

class SurveyController extends Controller
{
    use QuestionTypeTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = (blank(request()->limit)) ? 15 : request()->limit;

        $surveys =  Survey::latest()->paginate($limit);

        return response()->json([
            'status' => 200,
            'data' => SurveyResource::collection($surveys),
            'pagination' => paginatedResponse($surveys),
            'message' => 'Surveys retrieved successfully',
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validateQuestionnaireQuestionsAndAnswers();

        $questionnaire = Questionnaire::whereUuid($request->questionnaire_id)->with(['questions.answers'])->first();

        throw_if(
            blank( $questionnaire ),
                new CustomException(message: 'Record not found', status: 404)
        );

        $survey = $questionnaire->surveys()->create([
                'name' => Arr::get($request, 'respondent.name'),
                'email' => Arr::get($request, 'respondent.email')
        ]);


        $this->attachQuestionAnswersToSurveyResponse($questionnaire, $survey);

        return response()->json([
            'status' => 200,
            'data' => [],
            'message' => 'Thank you for the survey !',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $questionnnaireId)
    {
        $questionnaire = Questionnaire::whereUuid($questionnnaireId)->with(['questions.answers'])->first();

        throw_if(
            blank( $questionnaire ),
            new CustomException(message: 'Record not found', status: 404)
        );


        return response()->json([
            'status' => 200,
            'data' => new QuestionnaireWithQuestionResource( $questionnaire ),
            'message' => 'Questionnaire retrieved successfully',
        ]);
    }

    public function result(string $surveyId)
    {
        $survey = Survey::whereUuid($surveyId)->with(['questionnaire.questions.answers.responses'])->first();

        throw_if(
            blank( $survey ),
            new CustomException(message: 'Record not found', status: 404)
        );


        return response()->json([
            'status' => 200,
            'data' => new SurveyResultResource( $survey ),
            'message' => 'Survey retrieved successfully',
        ]);
    }


    private function attachQuestionAnswersToSurveyResponse(Questionnaire $questionnaire, Survey $survey)
    {
        $survey = Survey::whereId($survey->id)->first();

        foreach( request('responses') as $response ) {

                $question  = $questionnaire->questions()->whereUuid( Arr::get($response, 'question_id') )->first();

                if( $question->questiontype->type == 'checkbox' ) {

                    foreach( Arr::get($response , 'answer_id')  as $answerId ) {

                        $answer = Answer::where('question_id', $question->id)->whereUuid( $answerId )->first();

                        $survey->responses()->create([
                            'survey_id' => $survey->id,
                            'question_id' => $question->id,
                            'answer_id' => $answer->id
                        ]);
                    
                    }

                } 
                
                else {

                    $value = null;

                    $answer = Answer::where('question_id', $question->id)->whereUuid( Arr::get($response , 'answer_id') )->first();
    
                    if( $question->questiontype->type == 'text' ) {
    
                        $value = Arr::get( $response , 'meta.value' );
                    } 
    
                    else if( $question->questiontype->type == 'image' ) {
                        $value = $this->generateImage( Arr::get( $response , 'meta.value' ) ) ;
                        
                    } 
    
                    $response = $survey->responses()->create([
                        'survey_id' => $survey->id,
                        'question_id' => $question->id,
                        'answer_id' => $answer->id,
                        'value' => $value
                    ]);
                }

        }
        
    } 

    private function ensureValidQuestionTypeformat(QuestionType $questionType)
    {
        if( $questionType->type == 'checkbox' ) {

            throw_if(
            ! is_array( request('answers') ) || blank(  request('answers') ),
                new CustomException(message: 'Multiple Choice must be in array format', status: 400)
            );

        } 

    }


    public static function generateImage($img)
    {
        // Validate input: Check if the input is a valid base64 encoded string
        if (!preg_match('/^data:image\/(\w+);base64,/', $img, $type)) {
            throw new \InvalidArgumentException('Invalid image data');
        }

        // Extract the base64 encoded image string
        $image_parts = explode(";base64,", $img);
        if (count($image_parts) !== 2) {
            throw new \InvalidArgumentException('Invalid base64 image data');
        }

        // Decode the base64 image data
        $image_base64 = base64_decode($image_parts[1]);
        if ($image_base64 === false) {
            throw new \RuntimeException('Base64 decode failed');
        }

        // Generate a unique image path
        $image_extension = 'png';
        $image_name = uniqid(rand(), true) . '.' . $image_extension;
        $image_path = 'uploaded_images/' . $image_name;

        // Save the image to the specified disk
        Storage::disk('public')->put($image_path, $image_base64);

        return $image_path;
    }

    private function validateQuestionnaireQuestionsAndAnswers()
    {
        $questionnaire = Questionnaire::whereUuid(request('questionnaire_id'))->first();
        
        throw_if(
            blank($questionnaire),
            new CustomException(message: 'Questionnaire not found', status: 404)
        );
        
        $missingQuestionIds = [];
        $missingQuestionAnswerIds = [];
    
        foreach (request('responses') as $response) {
            $questionId = Arr::get($response, 'question_id');
            $question = Question::whereUuid($questionId)
                                ->where('questionnaire_id', $questionnaire->id)
                                ->first();
            
            if (blank($question)) {
                $missingQuestionIds[] = $questionId;
                continue;
            }
    
            if ($question->questiontype->type == 'checkbox') {
                foreach (Arr::get($response, 'answer_id', []) as $answerId) {
                    $answer = Answer::where('question_id', $question->id)->whereUuid($answerId)->first();
                    
                    if (blank($answer)) {
                        $missingQuestionAnswerIds[] = $answerId;
                    }
                }
            } else {
                $answerId = Arr::get($response, 'answer_id');
                $answer = Answer::where('question_id', $question->id)->whereUuid($answerId)->first();
                
                if (blank($answer)) {
                    $missingQuestionAnswerIds[] = $answerId;
                }
            }
        }
    
        throw_if(
            !blank($missingQuestionIds),
            new CustomException(message: 'Questions with id not found: ' . implode(', ', $missingQuestionIds), status: 404)
        );
    
        throw_if(
            !blank($missingQuestionAnswerIds),
            new CustomException(message: 'Answers with id not found: ' . implode(', ', $missingQuestionAnswerIds), status: 404)
        );
    }

}
