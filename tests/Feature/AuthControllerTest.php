<?php

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('pt_BR');
    }

    public function testRegisterWithIncorrectPasswordPassedInConfirmation(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'password_confirmation' => $this->faker->password
        ];

        $response = $this->post('/api/register', $data);

        $strict = false;

        $expected = [
            'error' => 'Passwords do not match'
        ];

        $response->assertJson($expected, $strict);
    }

    public function testRegisterReceivingSuccessMessage(): void
    {
        $password = $this->faker->password;

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post('/api/register', $data);

        $expected = [
            'token'
        ];

        $response->assertJsonStructure($expected);
    }
}
