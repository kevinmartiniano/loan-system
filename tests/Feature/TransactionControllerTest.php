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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('pt_BR');
        $this->artisan('db:seed');
        $this->artisan('passport:install');
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
        $walletLojist = Wallet::factory(1)->create();

        $userLojist = $walletLojist->user;
        $userLojist->user_type_id = UserType::LOJIST;
        $userLojist->save();

        $walletDefault = Wallet::factory(1)->create();
        $userDefault = $walletDefault->user;

        $dataTransaction = [
            'value' => 100.00,
            'payer' => $userLojist->id,
            'payee' => $userDefault->id
        ];

        $data = [
            'email' => $userLojist['email'],
            'password' => 'password'
        ];

        $response = $this->post('/api/login', $data, [
            'Accept' => 'application/json'
        ]);

        $json = json_decode($response->getContent());

        $header = [
            'Authorization' => 'Bearer ' . $json->token
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, $header);

        $expected = [
            'error' => 'You are not allowed to perform this action'
        ];

        $response->assertJson($expected);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testCreateTransferWithUserFromIsTypeDefaultExpectingCreated(): void
    {
        $walletValue = 5000;

        $walletLojist = Wallet::factory(1)->create();
        $walletLojist->value = $walletValue;
        $walletLojist->save();

        $userLojist = $walletLojist->user;
        $userLojist->user_type_id = UserType::LOJIST;
        $userLojist->save();

        $walletDefault = Wallet::factory(1)->create();
        $userDefault = $walletDefault->user;

        $dataTransaction = [
            'value' => 100.00,
            'payer' => $userDefault->id,
            'payee' => $userLojist->id
        ];

        $data = [
            'email' => $userLojist->email,
            'password' => 'password'
        ];


        $response = $this->post('/api/login', $data, [
            'Accept' => 'application/json'
        ]);

        $json = json_decode($response->getContent());

        $header = [
            'Authorization' => 'Bearer ' . $json->token
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, $header);

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
        $firstWallet = Wallet::factory(1)->create();
        $firstUser = $firstWallet->user;

        $secondWallet = Wallet::factory(1)->create();
        $secondUser = $secondWallet->user;

        $higherValue = 8000000;

        $dataTransaction = [
            'value' => $higherValue,
            'payer' => $secondUser->id,
            'payee' => $firstUser->id
        ];

        $data = [
            'email' => $secondUser->email,
            'password' => 'password'
        ];

        $response = $this->post('/api/login', $data, [
            'Accept' => 'application/json'
        ]);

        $json = json_decode($response->getContent());

        $header = [
            'Authorization' => 'Bearer ' . $json->token
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, $header);

        $expected = [
            'error' => 'Limit value exceeded'
        ];

        $response->assertJson($expected);

        $response->assertStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testTransferDontSendingRequiredFieldExpectingUnprocessableEntity(): void
    {
        $walletValue = 5000;

        $walletLojist = Wallet::factory(1)->create();
        $walletLojist->value = $walletValue;
        $walletLojist->save();

        $userLojist = $walletLojist->user;
        $userLojist->user_type_id = UserType::LOJIST;
        $userLojist->save();

        $walletDefault = Wallet::factory(1)->create();
        $userDefault = $walletDefault->user;

        $data = [
            'email' => $userLojist->email,
            'password' => 'password'
        ];


        $response = $this->post('/api/login', $data, [
            'Accept' => 'application/json'
        ]);

        $json = json_decode($response->getContent());

        $dataTransaction = [
            'value' => 100.00,
            'payer' => '',
            'payee' => $userLojist->id
        ];

        $header = [
            'Authorization' => 'Bearer ' . $json->token,
            'Accept' => 'application/json'
        ];

        $response = $this->post('/api/transaction/', $dataTransaction, $header);

        $expected = [
            "error" => [
                "payer" => [
                    "The payer field is required."
                ]
            ]
        ];

        $response->assertJson($expected);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
