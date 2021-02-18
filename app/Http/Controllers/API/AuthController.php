<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8',
        ]);

        if($request->password_confirmation != $request->password) {
            $response = [
                'error' => 'Passwords do not match'
            ];

            return response()->json($response, Response::HTTP_PRECONDITION_FAILED);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('createToken')->accessToken;

        $response = [
            'token' => $token
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $auth = Auth::attempt($data);

        if ($auth) {
            $user = User::where('email', '=', $request->email)->first();

            $token = $user->createToken('createToken')->accessToken;

            $response = [
                'token' => $token
            ];

            return response()->json($response, Response::HTTP_OK);
        } else {
            $response = [
                'error' => 'Unauthorized'
            ];

            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }
    }
}
