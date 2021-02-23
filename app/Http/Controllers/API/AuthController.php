<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
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
    ) {
        $this->authService = $authService;
        $this->walletService = $walletService;
    }
    /**
     * @OA\Post(
     *   tags={"Auth"},
     *   path="/api/register/",
     *   description="Route to register a user.",
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="document", type="string"),
     *         @OA\Property(property="password", type="string"),
     *         @OA\Property(property="password_confirmation", type="string"),
     *         @OA\Property(property="user_type_id", type="integer"),
     *         required={
     *           "name",
     *           "email",
     *           "document",
     *           "password",
     *           "password_confirmation",
     *           "user_type_id"
     *         }
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="A user is logged and a token response is received"
     *   )
     * )
     *
     * Registration
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
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
     * @OA\Post(
     *   tags={"Auth"},
     *   path="/api/login/",
     *   description="Route to login a user existing in database.",
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string"),
     *         required={"name", "password"}
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="A user is logged and a token response is received"
     *   )
     * )
     *
     * Login
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
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
