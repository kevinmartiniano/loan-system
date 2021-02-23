<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $this->validate($request, [
                'value' => 'required|numeric',
                'payer' => 'required|integer',
                'payee' => 'required|integer'
            ]);

            $transaction = $this->transactionService->sendTransaction($request->all());

            return response()->json($transaction, Response::HTTP_CREATED);

        } catch (ValidationException $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_METHOD_NOT_ALLOWED);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());

        }

    }
}
