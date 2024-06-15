<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if( $user && Hash::check( $request->password, $user->password ) ) {

            $data['token'] = $user->createToken('main')->plainTextToken;

            $data['user'] = new UserResource($user);

            return response()->json([
                'status' => 200,
                'data' => $data,
                'message' => 'User login successfully',
            ], 200);
        } 

        return response()->json([
            'status' => 401,
            'message' => 'Invalid login credentials',
        ], 401);
    }

}
