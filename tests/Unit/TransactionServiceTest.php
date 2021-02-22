<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\TransactionService;
use App\Services\WalletService;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('pt_BR');
        $this->artisan('db:seed');
    }

    private function fakerTransaction(User $firstUser, User $secondUser): array
    {
        return [
            'value' => $this->faker->number,
            'payer' => $firstUser->id,
            'payee' => $secondUser->id
        ];
    }

    public function testSendTransactionExpectingSuccess(): void
    {
        $payerWallet = Wallet::factory(1)->create();
        $payerValueBeforeTransaction = $payerWallet->value;

        $payeeWallet = Wallet::factory(1)->create();
        $payeeValueBeforeTransaction = $payeeWallet->value;

        $transactionService = new TransactionService(
            new UserRepository(new User()),
            new WalletRepository(new Wallet()),
            new TransactionRepository(new Transaction())
        );

        $data = [
            'value' => 1000,
            'payer' => $payerWallet->user_id,
            'payee' => $payeeWallet->user_id
        ];

        $transactionService->sendTransaction($data);

        $this->assertDatabaseHas('wallets', [
            'id' => $payerWallet->id,
            'value' => $payerValueBeforeTransaction - 1000
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $payeeWallet->id,
            'value' => $payeeValueBeforeTransaction + 1000
        ]);
    }

    public function testSendTransactionExpectingNotAllowedException(): void
    {
        $payerWallet = Wallet::factory(1)->create();
        $payerUser = $payerWallet->user;
        $payerUser->user_type_id = UserType::LOJIST;
        $payerUser->save();

        $payeeWallet = Wallet::factory(1)->create();

        $transactionService = new TransactionService(
            new UserRepository(new User()),
            new WalletRepository(new Wallet()),
            new TransactionRepository(new Transaction())
        );

        $data = [
            'value' => 1000,
            'payer' => $payerWallet->user_id,
            'payee' => $payeeWallet->user_id
        ];

        $this->expectException(Exception::class);

        $transactionService->sendTransaction($data);
    }
}
