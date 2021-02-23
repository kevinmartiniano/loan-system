<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\AuthService;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class AuthController extends Controller
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
    public function register(Request $request): JsonResponse
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

        return response()->json($response, Response::HTTP_CREATED);
    }

    /**
     * Login
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $user = $this->authService->authenticate($data)->first();

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
