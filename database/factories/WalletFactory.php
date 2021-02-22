<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Faker::create('pt_BR');
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => $this->faker->randomNumber(5),
            'user_id' => User::factory(1)->create()->id
        ];
    }
}
