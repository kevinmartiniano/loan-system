<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_types')->insert([
            'id' => 1,
            'name' => 'lojist',
            'description' => '',
        ]);

        DB::table('user_types')->insert([
            'id' => 2,
            'name' => 'default',
            'description' => '',
        ]);
    }
}
