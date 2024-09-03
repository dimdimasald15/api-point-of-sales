<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Retrieve all users and assign an employee to each
        $users = User::where('role', 'employee')->get();

        foreach ($users as $index => $user) {
            Employee::create([
                'user_id' => $user->id,  // Assign user_id to the employee
                'username' => 'test' . ($index + 1),
                'password' => Hash::make('test' . ($index + 1)),
                'token' => 'test' . ($index + 1),
            ]);
        }
    }
}
