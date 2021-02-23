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

    public function sendTransaction(array $data): ?Transaction
    {
        $payer = $this->userRepository->find($data['payer']);
        $payee = $this->userRepository->find($data['payee']);

        $transactionData = [
            'value' => $data['value'],
            'from_wallet_id' => $payer->wallet->id,
            'to_wallet_id' => $payee->wallet->id,
        ];

        $transaction = $this->createTransaction($transactionData);

        $validate = $this->validate($transaction, $payer->wallet, $payee->wallet);

        if($validate) {
            $transaction = $this->transactionRepository->update([
                'is_valid' => true
            ], $transaction->id);
        }

        return $transaction;
    }

    private function createTransaction(array $data)
    {
        return $this->transactionRepository->create($data);
    }

    private function validate(
        Transaction $transaction,
        Wallet $payerWallet,
        Wallet $payeeWallet
    ): bool
    {
        return DB::transaction(function () use (
            $payerWallet,
            $payeeWallet,
            $transaction
        ) {
            $isValid = false;
            $authorized = $this->request();

            if ($authorized['message'] == 'Autorizado') {
                $isValid = true;

                $subValue = $payerWallet->value - $transaction->value;

                $this->walletRepository->update([
                    'value' => $subValue
                ], $payerWallet->id);

                $sumValue = $payeeWallet->value + $transaction->value;

                $this->walletRepository->update([
                    'value' => $sumValue
                ], $payeeWallet->id);

                ProccessNotification::dispatch();

                return $isValid;
            }

            return $isValid;
        });
    }

    private function request(): array
    {
        $httpClient = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

        return $httpClient->json();
    }
}
