<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        $this->seed([
            UserSeeder::class,
            EmployeeSeeder::class,
        ]);

        $response = $this->post('/api/users/login', [
            'username' => 'test1',
            'password' => 'test1',
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'username',
                'token',
                'firstname',
                'lastname',
                'email',
            ],
        ]);

        // Use assertDatabaseHas to check that the token has been set in the database
        $this->assertDatabaseHas('employees', [
            'username' => 'test1',
            'token' => $response->json('data.token'),
        ]);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $response = $this->post('/api/users/login', [
            'username' => 'test33',
            'password' => 'test',
        ]);

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Username or password wrong',
                ],
            ],
        ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->post('/api/users/login', [
            'username' => 'test1',
            'password' => 'salah',
        ]);

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'Username or password wrong',
                ],
            ],
        ]);
    }

    public function testCreateEmployeeSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);
        $employeeData = $this->getEmployeeData();
        // Mengirim request ke endpoint API
        $response = $this->json('POST', '/api/users', $employeeData, $this->getAuthHeaders());
        $response->assertStatus(201);
        // Mengecek struktur respons JSON
        $response->assertJsonStructure([
            'data' => [
                'username',
                'firstname',
                'lastname',
                'email',
            ]
        ]);

        // Mengecek beberapa nilai spesifik dalam respons JSON
        $response->assertJsonFragment([
            'username' => $employeeData['username'],
            'firstname' => $employeeData['firstname'],
            'lastname' => $employeeData['lastname'],
            'email' => $employeeData['email'],
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
            "role" => "employee",
            "username" => "test3",
            "password" => "test3",
        ];
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
        $response->assertStatus(200);
        // Mengecek struktur respons JSON
        $response->assertJsonStructure([
            'data' => [
                'username',
                'firstname',
                'lastname',
                'email',
            ]
        ]);
        // Mengecek beberapa nilai spesifik dalam respons JSON
        $response->assertJsonFragment([
            "username" => "test1",
            "firstname" => "test",
            "lastname" => "test",
            "email" => "test@mail.com"
        ]);

        $response->assertJsonMissing([
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

        $oldUser = Employee::where('username', 'test1')->first();

        $response = $this->patch('/api/users/current', [
            'password' => 'rahasia'
        ], [
            'Authorization' => "test1"
        ]);

        $response->assertStatus(200);
        // Mengecek struktur respons JSON
        $response->assertJsonStructure([
            'data' => [
                'username',
                'firstname',
                'lastname',
                'email',
            ]
        ]);
        // Mengecek beberapa nilai spesifik dalam respons JSON
        $response->assertJsonFragment([
            "username" => "test1",
            "firstname" => "test",
            "lastname" => "test",
            "email" => "test@mail.com"
        ]);

        $newUser = Employee::where('username', 'test1')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }
    public function testUpdateUsernameSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $oldUser = Employee::where('username', 'test1')->first();

        $response = $this->patch('/api/users/current', [
            'username' => 'dimas',
        ], [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(200);
        // Mengecek struktur respons JSON
        $response->assertJsonStructure([
            'data' => [
                'username',
                'firstname',
                'lastname',
                'email',
            ]
        ]);
        // Mengecek beberapa nilai spesifik dalam respons JSON
        $response->assertJsonFragment([
            "username" => "dimas",
            "firstname" => "test",
            "lastname" => "test",
            "email" => "test@mail.com"
        ]);

        $newUser = Employee::where('username', 'dimas')->first();
        self::assertNotEquals($oldUser->username, $newUser->username);
    }
    public function testUpdateFirstnameSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $oldUser = Employee::where('username', 'test1')->first();
        $oldFirstname = $oldUser->user->firstname;
        $response = $this->patch('/api/users/current', [
            'firstname' => 'dimas',
            'lastname' => 'aldi',
            "email" => "dimas@mail.com"
        ], [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(200);
        // Mengecek struktur respons JSON
        $response->assertJsonStructure([
            'data' => [
                'username',
                'firstname',
                'lastname',
                'email',
            ]
        ]);
        // Mengecek beberapa nilai spesifik dalam respons JSON
        $response->assertJsonFragment([
            "username" => "test1",
            "firstname" => "dimas",
            "lastname" => "aldi",
            "email" => "dimas@mail.com"
        ]);

        $newUser = Employee::where('username', 'test1')->first();
        self::assertNotEquals($oldFirstname, $newUser->user->firstname);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->patch('/api/users/current', [
            'username' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
        ], [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(400)->assertJson([
            "errors" => [
                'username' => [
                    'The username must not be greater than 100 characters.'  // Adjust the message here
                ],
            ],
        ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(200)->assertJson([
            "data" => true,
        ]);

        $employee = Employee::where('username', 'test1')->first();
        self::assertNull($employee->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class, EmployeeSeeder::class]);

        $response = $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => "salah",
        ]);

        $response->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ],
        ]);
    }
}
