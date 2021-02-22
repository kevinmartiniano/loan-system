<?php

namespace App\Services;

use App\Jobs\ProccessNotification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransactionService
{

    private UserRepositoryInterface $userRepository;

    private WalletRepositoryInterface $walletRepository;

    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        WalletRepositoryInterface $walletRepository,
        TransactionRepositoryInterface $transactionRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function sendTransaction(array $data): JsonResponse
    {
        $payer = $this->userRepository->find($data['payer']);
        $payee = $this->userRepository->find($data['payee']);

        if (!$this->isAllowed($payer)) {
            $response = 'You are not allowed to perform this action';

            throw new Exception($response, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($this->isExceededValue($payer->wallet, $data['value'])) {
            $response = 'Limit value exceeded';

            throw new Exception($response, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $transactionData = [
            'value' => $data['value'],
            'from_wallet_id' => $payer->wallet->id,
            'to_wallet_id' => $payee->wallet->id,
        ];

        $transaction = $this->createTransaction($transactionData);

        return $this->validateTransaction($transaction, $payer->wallet, $payee->wallet);
    }

    private function isAllowed(User $user): bool
    {
        return $user->user_type->id != UserType::LOJIST;
    }

    private function isExceededValue(Wallet $payerWallet, float $transactionValue): bool
    {
        return $payerWallet->value < $transactionValue;
    }

    private function createTransaction(array $data)
    {
        return Transaction::create($data);
    }

    private function validateTransaction(
        Transaction $transaction,
        Wallet $payerWallet,
        Wallet $payeeWallet
    )
    {
        return DB::transaction(function () use (
            $payerWallet,
            $payeeWallet,
            $transaction
        ) {

            $authorized = $this->request();

            if ($authorized['message'] == 'Autorizado') {
                $subValue = $payerWallet->value - $transaction->value;

                $this->walletRepository->update([
                    'value' => $subValue
                ], $payerWallet->id);

                $sumValue = $payeeWallet->value + $transaction->value;

                $this->walletRepository->update([
                    'value' => $sumValue
                ], $payeeWallet->id);

                $this->transactionRepository->update([
                    'is_valid' => true
                ], $transaction->id);

                ProccessNotification::dispatch();

                return response()->json($transaction, Response::HTTP_CREATED);
            }
        });
    }

    private function request(): array
    {
        $httpClient = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

        return $httpClient->json();
    }
}
