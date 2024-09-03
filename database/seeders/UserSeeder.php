<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'phone_number' => '011111',
            'email' => 'test@mail.com',
            'address' => 'test',
            'postal_code' => '1111',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'role' => 'employee',
            'comments' => 'test',
        ]);
        User::create([
            'firstname' => 'test_2',
            'lastname' => 'test_2',
            'phone_number' => '022222',
            'email' => 'test_2@mail.com',
            'address' => 'test_2',
            'postal_code' => '2222',
            'city' => 'test_2',
            'province' => 'test_2',
            'country' => 'test_2',
            'role' => 'employee',
            'comments' => 'test_2',
        ]);
        User::create([
            'firstname' => 'test_3',
            'lastname' => 'test_3',
            'phone_number' => '033333',
            'email' => 'test_3@mail.com',
            'address' => 'test_3',
            'postal_code' => '3333',
            'city' => 'test_3',
            'province' => 'test_3',
            'country' => 'test_3',
            'role' => 'customer',
            'comments' => 'test_3',
        ]);
        User::create([
            'firstname' => 'test_4',
            'lastname' => 'test_4',
            'phone_number' => '044444',
            'email' => 'test_4@mail.com',
            'address' => 'test_4',
            'postal_code' => '4444',
            'city' => 'test_4',
            'province' => 'test_4',
            'country' => 'test_4',
            'role' => 'customer',
            'comments' => 'test_4',
        ]);
        User::create([
            'firstname' => 'test_5',
            'lastname' => 'test_5',
            'phone_number' => '055555',
            'email' => 'test_5@mail.com',
            'address' => 'test_5',
            'postal_code' => '5555',
            'city' => 'test_5',
            'province' => 'test_5',
            'country' => 'test_5',
            'role' => 'supplier',
            'comments' => 'test_5',
        ]);
    }
}
