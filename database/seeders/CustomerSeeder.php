<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('role', 'customer')->get();
        foreach ($users as $i => $user) {
            Customer::create([
                'user_id' => $user->id,
                'account_number' => "01" . ($i + 1) . ($i + 1) . ($i + 1) . ($i + 1),
                'taxable' => 1
            ]);
        }
    }
}
