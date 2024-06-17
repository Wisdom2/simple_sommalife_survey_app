<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;
use App\Http\Resources\QuestionTypeResource;

class QuestionTypeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $questionTypes = QuestionType::all();

        return response()->json([
            'status' => 200,
            'data' => QuestionTypeResource::collection($questionTypes),
            'message' => 'Types retrieved successfully',
        ]);
        
    }
}
