<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @OA\Post(
     *   tags={"Transactions"},
     *   path="/api/transaction/",
     *   description="Create a UserTypes",
     *   @OA\RequestBody(
     *     @OA\MediaType(mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="value", type="number", format="float"),
     *         @OA\Property(property="payer", type="integer"),
     *         @OA\Property(property="payee", type="integer"),
     *         required={"value", "payer", "payee"}
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="201",
     *     description="transaction created"
     *   )
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->sendTransaction($request->all());

        return response()->json($transaction, Response::HTTP_CREATED);
    }
}
