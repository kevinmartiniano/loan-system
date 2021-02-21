<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProccessNotification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, [
                'value' => 'required|numeric',
                'payer' => 'required|integer',
                'payee' => 'required|integer'
            ]);

            $user = User::find($request->payer);

            if ($user->user_type->id == UserType::LOJIST) {
                $response = [
                    'error' => 'You are not allowed to perform this action'
                ];

                return response()->json($response, Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $walletPayer = Wallet::where('user_id', '=', $request->payer)->first();
            $walletPayee = Wallet::where('user_id', '=', $request->payee)->first();

            if ($walletPayee->value < $request->value) {
                $response = [
                    'error' => 'Limit value exceeded'
                ];

                return response()->json($response, Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $data = [
                'value' => $request->value,
                'from_wallet_id' => $walletPayer->id,
                'to_wallet_id' => $walletPayee->id,
            ];

            $transaction = Transaction::create($data);

            return DB::transaction(function () use (
                    $walletPayer,
                    $walletPayee,
                    $request,
                    $transaction
                ) {

                // TODO: Validar request antes de efetivar a transferencia
                $httpClient = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
                $authorized = $httpClient->json();

                if ($authorized['message'] == 'Autorizado') {
                    $walletPayer->value = $walletPayer->value - $request->value;
                    $walletPayer->save();

                    $walletPayee->value = $walletPayee->value + $request->value;
                    $walletPayee->save();

                    $transaction->is_valid = true;
                    $transaction->save();

                    ProccessNotification::dispatch();

                    return response()->json($transaction, Response::HTTP_CREATED);
                }

            });

        } catch (ValidationException $e) {

            return response()->json($e->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED);

        } catch (Exception $e) {

            return response()->json($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }
}
