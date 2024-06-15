<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DefaultUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(['email' => 'admin@sommalife.com'], [
            'name' => 'Administrator',
            'email' => 'admin@sommalife.com',
            'email_verified_at' => now(),
            'password' => 'p@$$W0rd',
        ]);
    }
}
