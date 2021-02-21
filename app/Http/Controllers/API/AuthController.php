<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use App\Services\AuthService;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class AuthController extends BaseController
{
    private AuthService $authService;

    private WalletService $walletService;

    public function __construct(
        AuthService $authService,
        WalletService $walletService
    )
    {
        $this->authService = $authService;
        $this->walletService = $walletService;
    }
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
            $user = $this->authService->createUser($request->all());

            $data = [
                'value' => Wallet::EMPTY_WALLET_VALUE,
                'user_id' => $user->id
            ];


            $this->walletService->create($data);

        } catch (ValidationException $e) {

            $response = [
                'error' => $e->getMessage()
            ];

            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {

            $response = [
                'error' => $e->getMessage()
            ];

            return response()->json($response, $e->getCode());

        }

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
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $user = $this->authService->authenticate($data);

        if ($user) {
            $token = $user->createToken('createToken')->accessToken;

            $response = [
                'token' => $token
            ];

            return response()->json($response, Response::HTTP_OK);
        }

        $response = [
            'error' => 'Unauthorized'
        ];

        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }
}
