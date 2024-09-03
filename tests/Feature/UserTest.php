<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function testCreateCustomerSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users', $this->getCustomerData(), $this->getAuthHeaders());

        $response->assertStatus(201)->assertJson([
            "data" => array_merge($this->getCommonData(), [
                "account_number" => "33333",
                "taxable" => 1,
            ]),
        ]);
    }
    public function testCreateSupplierSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users', $this->getSupplierData(), $this->getAuthHeaders());

        $response->assertStatus(201)->assertJson([
            "data" => array_merge($this->getCommonData(), [
                "account_number" => "33333",
            ]),
        ]);
    }
    public function testCreateNewEmployeeSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users', $this->getEmployeeData(), $this->getAuthHeaders());

        $response->assertStatus(201)->assertJson([
            "data" => array_merge($this->getCommonData(), [
                "username" => "test3",
            ]),
        ]);
    }
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users', $this->getInvalidData(), $this->getAuthHeaders());

        $response->assertStatus(400)->assertJson([
            "errors" => [
                'firstname' => ["The firstname field is required."],
                'lastname' => ["The lastname field is required."],
                'email' => ["The email must be a valid email address."],
            ],
        ]);
    }
    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users', $this->getInvalidData(), $this->getWrongAuthHeaders());

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => ["unauthorized"],
            ],
        ]);
    }
    // Helper methods
    protected function getCommonData(): array
    {
        return [
            "firstname" => "test3",
            "lastname" => "test3",
            "phone_number" => "033333",
            "email" => "test3@mail.com",
            "address" => "test3",
            "postal_code" => "test3",
            "city" => "test3",
            "province" => "test3",
            "country" => "test3",
            "comments" => "lorem ipsum",
        ];
    }
    protected function getCustomerData(): array
    {
        return array_merge($this->getCommonData(), [
            "role" => "customer",
            "account_number" => "33333",
        ]);
    }
    protected function getSupplierData(): array
    {
        return array_merge($this->getCommonData(), [
            "role" => "supplier",
            "account_number" => "33333",
        ]);
    }
    protected function getEmployeeData(): array
    {
        return array_merge($this->getCommonData(), [
            "role" => "employee",
            "username" => "test3",
            "password" => "test3",
        ]);
    }
    protected function getInvalidData(): array
    {
        return array_merge($this->getCommonData(), [
            "firstname" => "",
            "lastname" => "",
            "email" => "test3mail.com",
        ]);
    }
    protected function getAuthHeaders(): array
    {
        return ['authorization' => 'test1'];
    }
    protected function getWrongAuthHeaders(): array
    {
        return ['authorization' => 'salah'];
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->get('/api/users/current', [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(200)->assertJson([
            "data" => [
                "firstname" => "test",
                "lastname" => "test",
                "phone_number" => "011111",
                "email" => "test@mail.com",
                "role" => "test@mail.com",
                "address" => "test",
                "postal_code" => "1111",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "comments" => "test",
                "username" => "test1"
            ],
        ])->assertJsonMissing([
            "data" => [
                'password' => 'test1',
            ],
        ]);
    }

    public function testGetUnauthorized()
    {
        $response = $this->get('/api/users/current');

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    "unauthorized"
                ],
            ],
        ]);
    }
    public function testGetInvalidToken()
    {
        $response = $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ]);

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    "unauthorized"
                ],
            ],
        ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $oldUser = User::where('username', 'test')->first();

        $response = $this->patch('/api/users/current', [
            'password' => 'rahasia'
        ], [
            'Authorization' => "test",
        ]);

        $response->assertStatus(200)->assertJson([
            "data" => [
                'username' => 'test',
                'name' => 'test',
            ],
        ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }
    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $oldUser = User::where('username', 'test')->first();

        $response = $this->patch('/api/users/current', [
            'user' => 'dimas',
            'name' => 'dimas'
        ], [
            'Authorization' => "test",
        ]);

        $response->assertStatus(200)->assertJson([
            "data" => [
                'username' => 'test',
                'name' => 'dimas',
            ],
        ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->patch('/api/users/current', [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
        ], [
            'Authorization' => "test",
        ]);

        $response->assertStatus(400)->assertJson([
            "errors" => [
                'name' => [
                    'The name must not be greater than 100 characters.'  // Adjust the message here
                ],
            ],
        ]);
    }
}
