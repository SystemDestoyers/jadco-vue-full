<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Elnakieb',
            'email' => 'ahmedortegacr7@gmail.com',
            'password' => Hash::make('111'),
        ]);
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('111'),
        ]);
        // Create admin user
        User::create([
            'name' => 'MR: JEHAD',
            'email' => 'jad@jadco.co',
            'password' => Hash::make('111'),
        ]);
        User::create([
            'name' => 'Mohamed Ahmed',
            'email' => 'Ui-ux@hotmail.com',
            'password' => Hash::make('111'),
        ]);
    }
}
