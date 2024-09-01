<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            'name'=> 'User Test',
            'email'=> 'test@mail.com',
            'password'=> bcrypt('mypassword')
        ];

        User::create($user);
    }
}
