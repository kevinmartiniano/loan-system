<?php

namespace Tests\Feature;

use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class UserTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create('pt_BR');

        $this->artisan('passport:install');
    }

    private function fakerUserType(): array
    {
        return [
            'name' => $this->faker->text(50),
            'description' => $this->faker->text(255)
        ];
    }

    public function testGetAllUserTypes(): void
    {
        $data = $this->fakerUserType();

        $userType = UserType::create($data);

        $response = $this->get('/api/user-types');

        $response->assertJson($userType->all()->toArray());
    }

    public function testGetUserTypeByIdExpectingOk(): void
    {
        $data = $this->fakerUserType();

        $userType = UserType::create($data);

        $response = $this->get('/api/user-types/' . $userType->id);

        $response->assertJson($userType->toArray());
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUserTypeExpectingOk(): void
    {
        $data = $this->fakerUserType();

        $userType = UserType::create($data);

        $newName = $this->faker->text(50);

        $data['name'] = $newName;

        $response = $this->put('/api/user-types/' . $userType->id, $data);

        $userType->name = $newName;

        $response->assertJson($userType->toArray());
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUserTypeExpectingOk(): void
    {
        $data = $this->fakerUserType();

        $userType = UserType::create($data);

        $response = $this->delete('/api/user-types/' . $userType->id);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateUserTypeExpectingCreated(): void
    {
        $data = $this->fakerUserType();

        $response = $this->post('/api/user-types/', $data);

        $userTypeId = json_decode($response->getContent())->id;

        $userType = UserType::find($userTypeId);

        $expected = json_encode($userType->toArray());

        $expected = $userType->id;

        $this->assertEquals($expected, json_decode($response->getContent())->id);
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
