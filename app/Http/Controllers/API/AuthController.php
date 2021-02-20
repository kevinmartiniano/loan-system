<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class AuthController extends BaseController
{
    /**
     * Registration
     */
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|min:4',
                'email' => 'required|email',
                'document' => 'required|min:11|max:14',
                'password' => 'required|min:8',
                'password_confirmation' => 'required|min:8',
            ]);
        } catch (ValidationException $e) {
            $response = [
                'error' => $e->getMessage()
            ];

            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($request->password_confirmation != $request->password) {
            $response = [
                'error' => 'Passwords do not match'
            ];

            return response()->json($response, Response::HTTP_PRECONDITION_FAILED);
        }

        $userType = (!empty($request->user_type_id) ? $request->user_type_id : UserType::GENERAL);

        $userExists = User::where('email', '=', $request->email)
                                ->orWhere('document', '=', $request->document)
                                ->first();

        if($userExists) {
            $response = [
                'error' => 'User already exists!'
            ];

            return response()->json($response, Response::HTTP_CONFLICT);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'document' => $request->document,
            'password' => bcrypt($request->password),
            'user_type_id' => $userType
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
