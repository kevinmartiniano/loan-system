<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\WalletService;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class TransactionControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('pt_BR');
    }

    private function fakerUser(): array
    {
        $passLength = 8;
        $password = $this->faker->password($passLength);

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'document' => $this->faker->cpf(false),
            'password' => $password,
            'password_confirmation' => $password,
        ];
    }

    public function testCreateTransferWithUserFromIsTypeLojistExpectingNotAllowed(): void
    {
        UserType::create([
            'name' => 'lojist',
            'description' => ''
        ]);

        UserType::create([
            'name' => 'default',
            'description' => ''
        ]);

        $userLojistFaker = $this->fakerUser();
        $userLojistFaker['user_type_id'] = UserType::LOJIST;

        $userDefaultFaker = $this->fakerUser();

        $authService = new AuthService(new UserRepository(new User()));

        $userLojist = $authService->createUser($userLojistFaker);

        $data = [
            'value' => Wallet::EMPTY_WALLET_VALUE,
            'user_id' => $userLojist->id
        ];

        $walletService = new WalletService();

        $walletService->create($data);

        $userDefault = $authService->createUser($userDefaultFaker);

        $data = [
            'value' => Wallet::EMPTY_WALLET_VALUE,
            'user_id' => $userDefault->id
        ];

        $walletService = new WalletService();

        $walletService->create($data);

        $dataTransaction = [
            'value' => 100.00,
            'payer' => $userLojist->id,
            'payee' => $userDefault->id
        ];

        $response = $this->post('/api/transaction/', $dataTransaction);

        $expected = [
            'error' => 'You are not allowed to perform this action'
        ];

        $response->assertJson($expected);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testCreateTransferWithUserFromIsTypeDefaultExpectingCreated(): void
    {
        UserType::create([
            'name' => 'lojist',
            'description' => ''
        ]);

        UserType::create([
            'name' => 'default',
            'description' => ''
        ]);

        $userLojistFaker = $this->fakerUser();
        $userLojistFaker['user_type_id'] = UserType::LOJIST;

        $userDefaultFaker = $this->fakerUser();

        $authService = new AuthService(new UserRepository(new User()));

        $userLojist = $authService->createUser($userLojistFaker);

        $walletValue = 5000;

        $data = [
            'value' => $walletValue,
            'user_id' => $userLojist->id
        ];

        $walletService = new WalletService();

        $walletLojist = $walletService->create($data);

        $userDefault = $authService->createUser($userDefaultFaker);

        $data = [
            'value' => $walletValue,
            'user_id' => $userDefault->id
        ];

        $walletService = new WalletService();

        $walletDefault = $walletService->create($data);

        $dataTransaction = [
            'value' => 100.00,
            'payer' => $userDefault->id,
            'payee' => $userLojist->id
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, [
            'Accept' => 'application/json'
        ]);

        $expected = [
            'value',
            'is_valid',
            'from_wallet_id',
            'to_wallet_id'
        ];

        $walletLojistValidate = Wallet::find($walletLojist->id);
        $walletDefaultValidate = Wallet::find($walletDefault->id);

        $this->assertNotEquals($walletLojist->value, $walletLojistValidate->id);
        $this->assertNotEquals($walletDefault->value, $walletDefaultValidate->id);

        $response->assertJsonStructure($expected);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateTransferWithUserFromIsTypeDefaultAndAHigherValuePassedExpectingNotAllowed(): void
    {
        UserType::create([
            'name' => 'lojist',
            'description' => ''
        ]);

        UserType::create([
            'name' => 'default',
            'description' => ''
        ]);

        $userLojistFaker = $this->fakerUser();
        $userLojistFaker['user_type_id'] = UserType::LOJIST;

        $userDefaultFaker = $this->fakerUser();

        $authService = new AuthService(new UserRepository(new User()));

        $userLojist = $authService->createUser($userLojistFaker);

        $walletValue = 5000;

        $data = [
            'value' => $walletValue,
            'user_id' => $userLojist->id
        ];

        $walletService = new WalletService();

        $walletService->create($data);

        $userDefault = $authService->createUser($userDefaultFaker);

        $data = [
            'value' => $walletValue,
            'user_id' => $userDefault->id
        ];

        $walletService = new WalletService();

        $walletService->create($data);

        $higherValue = 5100.00;

        $dataTransaction = [
            'value' => $higherValue,
            'payer' => $userDefault->id,
            'payee' => $userLojist->id
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, [
            'Accept' => 'application/json'
        ]);

        $expected = [
            'error' => 'Limit value exceeded'
        ];

        $response->assertJson($expected);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
