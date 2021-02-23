<?php

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class AuthControllerTest extends TestCase
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
            'password_confirmation' => $password
        ];
    }

    public function testRegisterWithIncorrectPasswordPassedInConfirmation(): void
    {
        $data = $this->fakerUser();

        $passLength = 8;

        $data["password_confirmation"] = $this->faker->password($passLength);

        $response = $this->post('/api/register', $data);

        $strict = false;

        $expected = [
            'error' => 'Passwords do not match'
        ];

        $response->assertJson($expected, $strict);
        $response->assertStatus(Response::HTTP_PRECONDITION_FAILED);
    }

    public function testRegisterReceivingSuccessMessage(): void
    {
        $data = $this->fakerUser();

        $response = $this->post('/api/register', $data, [
            'Accept' => 'application/json'
        ]);

        $expected = [
            'token'
        ];

        $response->assertJsonStructure($expected, $response->getContent());
    }

    public function testRegisterWithDontSendRequiredFieldUnprocessableEntity(): void
    {
        $data = $this->fakerUser();

        $data["document"] = "";

        $response = $this->post('/api/register', $data);

        $expected = [
            'error' => 'The given data was invalid.'
        ];

        $response->assertJson($expected);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRegisterWithDuplicateDocumentOrEmailIsSendedExpectingConflict(): void
    {
        $data = $this->fakerUser();

        $this->post('/api/register', $data);

        $response = $this->post('/api/register', $data, [
            'Accept' => 'application/json'
        ]);

        $expected = [
            'error' => 'User already exists!'
        ];

        $response->assertJson($expected);
        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}
