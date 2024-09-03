<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Database\Seeders\SupplierSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, EmployeeSeeder::class, SupplierSeeder::class, SupplierSeeder::class]);
    }

    private function assertJsonResponse($response, array $expectedData)
    {
        $response->assertJsonStructure([
            'data' => [
                'firstname',
                'lastname',
                'email',
                'role',
                'address',
                'city',
                'province',
                'country',
                'postal_code',
                'comments',
                'user_id',
                'account_number',
            ]
        ]);
        $response->assertJsonFragment($expectedData);
    }

    protected function getCommonData(): array
    {
        return [
            "firstname" => "test1030",
            "lastname" => "test1030",
            "phone_number" => "010301030",
            "email" => "test1030@mail.com",
            "address" => "test1030",
            "postal_code" => "test1030",
            "city" => "test1030",
            "province" => "test1030",
            "country" => "test1030",
            "comments" => "lorem ipsum",
            "username" => "test1030",
            "password" => "test1030",
        ];
    }

    protected function getSupplierData(): array
    {
        return array_merge($this->getCommonData(), [
            "role" => "supplier",
            "account_number" => "test1030",
        ]);
    }

    public function testGetSuccess()
    {
        $response = $this->get('/api/suppliers/5', [
            'Authorization' => "test1",
        ]);
        $expectedData = [
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
            'account_number' => '011111',
        ];
        $response->assertStatus(200);
        $this->assertJsonResponse($response, $expectedData);

        $response->assertJsonMissing([
            "data" => [
                'password' => 'test1',
            ],
        ]);
    }

    public function testGetDetailUnauthorized()
    {
        $response = $this->get('/api/suppliers/4');

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    "unauthorized"
                ],
            ],
        ]);
    }

    public function testGetDetailInvalidToken()
    {
        $response = $this->get('/api/suppliers/4', [
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

    public function testListSuppliers()
    {
        // Send a GET request to the endpoint
        $response = $this->get('/api/suppliers', [
            'Authorization' => "test1",
        ]);

        // Assert the response status is 200
        $response->assertStatus(200);

        // Assert the JSON structure
        $response->assertJsonStructure([
            'data' => [
                '*' => [  // The asterisk '*' means the array can have multiple items
                    'id',
                    'user_id',
                    'account_number',
                    'firstname',
                    'lastname',
                    'email',
                ]
            ]
        ]);

        // Optionally assert the content of the response
        $suppliers = Supplier::all();
        $responseData = $response->json('data');

        $this->assertCount($suppliers->count(), $responseData);

        foreach ($suppliers as $index => $supplier) {
            $this->assertEquals($supplier->id, $responseData[$index]['id']);
            $this->assertEquals($supplier->user_id, $responseData[$index]['user_id']);
            $this->assertEquals($supplier->account_number, $responseData[$index]['account_number']);
            $this->assertEquals($supplier->user->firstname, $responseData[$index]['firstname']);
            $this->assertEquals($supplier->user->lastname, $responseData[$index]['lastname']);
            $this->assertEquals($supplier->user->email, $responseData[$index]['email']);
            // Add more fields to compare if needed
        }
    }

    public function testGetListUnauthorized()
    {
        $response = $this->get('/api/suppliers');

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    "unauthorized"
                ],
            ],
        ]);
    }
    public function testGetListInvalidToken()
    {
        $response = $this->get('/api/suppliers', [
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

    public function testCreateSupplierSuccess()
    {
        $supplierData = $this->getSupplierData();
        $response = $this->json('POST', '/api/suppliers', $supplierData, ['Authorization' => 'test1']);
        $expectedData = [
            'firstname' => $supplierData['firstname'],
            'lastname' => $supplierData['lastname'],
            'email' => $supplierData['email'],
            'phone_number' => $supplierData['phone_number'],
            'role' => $supplierData['role'],
            'address' => $supplierData['address'],
            'city' => $supplierData['city'],
            'province' => $supplierData['province'],
            'country' => $supplierData['country'],
            'postal_code' => $supplierData['postal_code'],
            'comments' => $supplierData['comments'],
            'account_number' => $supplierData['account_number'],
        ];
        $response->assertStatus(201);
        $this->assertJsonResponse($response, $expectedData);
    }

    public function testCreateFailed()
    {
        $response = $this->post('/api/suppliers', $this->getInvalidData(), ['Authorization' => 'test1']);

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
        $response = $this->post('/api/suppliers', $this->getInvalidData(), ['Authorization' => 'salah']);

        $response->assertStatus(401)->assertJson([
            "errors" => [
                'message' => ["unauthorized"],
            ],
        ]);
    }

    protected function getInvalidData(): array
    {
        return array_merge($this->getCommonData(), [
            "firstname" => "",
            "lastname" => "",
            "email" => "test5mail.com",
        ]);
    }

    // public function testUpdateAccountNumberSuccess()
    // {
    //     $oldUser = Supplier::where('account_number', '011111')->first();
    //     $oldAccountNumber = $oldUser->account_number;
    //     $response = $this->patch('/api/suppliers/4', [
    //         'account_number' => '015333',
    //     ], [
    //         'Authorization' => "test1",
    //     ]);

    //     $expectedData = [
    //         'account_number' => '013333',
    //     ];
    //     $response->assertStatus(200);
    //     $this->assertJsonResponse($response, $expectedData);

    //     $response->assertJsonMissing([
    //         "data" => [
    //             'password' => 'test1',
    //         ],
    //     ]);

    //     $newUser = Supplier::where('account_number', '013333')->first();
    //     self::assertNotEquals($oldAccountNumber, $newUser->account_number);
    // }
    // public function testUpdateFirstnameeSuccess()
    // {
    //     $oldUser = Supplier::find(1);
    //     $oldFirstname = $oldUser->user->firstname;
    //     $response = $this->patch('/api/suppliers/4', [
    //         'firstname' => 'aldi',
    //     ], [
    //         'Authorization' => "test1",
    //     ]);

    //     $expectedData = [
    //         'firstname' => 'aldi',
    //     ];
    //     $response->assertStatus(200);
    //     $this->assertJsonResponse($response, $expectedData);

    //     $response->assertJsonMissing([
    //         "data" => [
    //             'password' => 'test1',
    //         ],
    //     ]);

    //     $newUser = Supplier::find(1);
    //     self::assertNotEquals($oldFirstname, $newUser->user->firstname);
    // }

    public function testUpdateFailed()
    {
        $response = $this->patch('/api/suppliers/4', [
            'firstname' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'account_number' => '902939023893891989239029039209302'
        ], [
            'Authorization' => "test1",
        ]);

        $response->assertStatus(400)->assertJson([
            "errors" => [
                'firstname' => [
                    'The firstname must not be greater than 100 characters.'
                ],
                'account_number' => [
                    'The account number must not be greater than 20 characters.'
                ],
            ],
        ]);
    }
}
