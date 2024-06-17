<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\QuestionnaireController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', LoginController::class );


Route::middleware('auth:sanctum')->prefix('/questionnaires')->group(function () {
    Route::get('/', [QuestionnaireController::class, 'index'] );
    Route::get('/{id}', [QuestionnaireController::class, 'show'] );
    Route::post('/', [QuestionnaireController::class, 'store'] );
    Route::put('/{id}', [QuestionnaireController::class, 'update'] );
   Route::delete('/{questionnaireId}/questions/{questionId}', [QuestionnaireController::class, 'destroyQuestion'] );
});


Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/', [QuestionController::class, 'index'] );
    // Route::get('/{id}', [QuestionnaireController::class, 'show'] );
    Route::post('questionnaire/questions', [QuestionController::class, 'store'] );
    // Route::put('/{id}', [QuestionnaireController::class, 'update'] );
});


Route::get('surveys/questionnaires/{questionnnaireId}', [SurveyController::class, 'show'] );
Route::get('surveys/questionnaires', [SurveyController::class, 'index'] );
Route::get('surveys/{surveyId}', [SurveyController::class, 'result'] );
Route::post('surveys/questionnaires', [SurveyController::class, 'store'] );

Route::get('/types', QuestionTypeController::class )->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
