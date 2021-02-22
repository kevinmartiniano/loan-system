<?php

namespace App\Services;

use App\Jobs\ProccessNotification;
use App\Models\Transaction;
use App\Models\UserType;
use App\Models\Wallet;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransactionService
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function sendTransaction(array $data): JsonResponse
    {
        $user = $this->userRepository->find($data['payer']);

        if ($user->user_type->id == UserType::LOJIST) {
            $response = 'You are not allowed to perform this action';

            throw new Exception($response, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $walletPayer = Wallet::where('user_id', '=', $data['payer'])->first();
        $walletPayee = Wallet::where('user_id', '=', $data['payee'])->first();

        if ($walletPayee->value < $data['value']) {
            $response = 'Limit value exceeded';

            throw new Exception($response, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $data = [
            'value' => $data['value'],
            'from_wallet_id' => $walletPayer->id,
            'to_wallet_id' => $walletPayee->id,
        ];

        $transaction = Transaction::create($data);

        return DB::transaction(function () use (
            $walletPayer,
            $walletPayee,
            $data,
            $transaction
        ) {

            // TODO: Validar request antes de efetivar a transferencia
            $httpClient = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
            $authorized = $httpClient->json();

            if ($authorized['message'] == 'Autorizado') {
                $walletPayer->value = $walletPayer->value - $data['value'];
                $walletPayer->save();

                $walletPayee->value = $walletPayee->value + $data['value'];
                $walletPayee->save();

                $transaction->is_valid = true;
                $transaction->save();

                ProccessNotification::dispatch();

                return response()->json($transaction, Response::HTTP_CREATED);
            }
        });
    }
}
