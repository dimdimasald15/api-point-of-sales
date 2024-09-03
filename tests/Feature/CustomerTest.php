<?php

namespace Tests\Feature;

use App\Models\Customer;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, EmployeeSeeder::class, CustomerSeeder::class]);
        $this->assertTrue(Customer::count() > 0, "Customer seeding failed");
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
                'taxable',
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

    protected function getCustomerData(): array
    {
        return array_merge($this->getCommonData(), [
            "role" => "customer",
            "account_number" => "test1030",
        ]);
    }


    public function testUpdateAccountNumberSuccess()
    {
        $oldUser = Customer::find(1);
        $oldAccountNumber = $oldUser->account_number;
        $response = $this->patch('/api/customers/3', [
            'account_number' => '013333',
        ], [
            'Authorization' => "test1",
        ]);

        $expectedData = [
            'account_number' => '013333',
        ];
        $response->assertStatus(200);
        $this->assertJsonResponse($response, $expectedData);

        $response->assertJsonMissing([
            "data" => [
                'password' => 'test1',
            ],
        ]);

        $newUser = Customer::find(1);
        self::assertNotEquals($oldAccountNumber, $newUser->account_number);
    }
    // public function testUpdateTaxableSuccess()
    // {
    //     $oldUser = Customer::find(1);
    //     $oldTaxable = $oldUser->taxable;
    //     $response = $this->patch('/api/customers/3', [
    //         'taxable' => 0,
    //     ], [
    //         'Authorization' => "test1",
    //     ]);

    //     $expectedData = [
    //         'taxable' => 0,
    //     ];
    //     $response->assertStatus(200);
    //     $this->assertJsonResponse($response, $expectedData);

    //     $response->assertJsonMissing([
    //         "data" => [
    //             'password' => 'test1',
    //         ],
    //     ]);

    //     $newUser = Customer::where('account_number', '011111')->first();
    //     self::assertNotEquals($oldTaxable, $newUser->taxable);
    // }
    // public function testUpdateFirstnameSuccess()
    // {
    //     $oldUser = Customer::where('account_number', '011111')->first();
    //     $oldFirstname = $oldUser->user->firstname;
    //     $response = $this->patch('/api/customers/3', [
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

    //     $newUser = Customer::where('account_number', '011111')->first();
    //     self::assertNotEquals($oldFirstname, $newUser->user->firstname);
    // }
    public function testGetSuccess()
    {
        $response = $this->get('/api/customers/3', [
            'Authorization' => "test1",
        ]);
        $expectedData = [
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
            'account_number' => '011111',
            'taxable' => 1
        ];
        $response->assertStatus(200);
        $this->assertJsonResponse($response, $expectedData);

        $response->assertJsonMissing([
            "data" => [
                'password' => 'test1',
            ],
        ]);
    }
    public function testListCustomers()
    {
        // Send a GET request to the endpoint
        $response = $this->get('/api/customers', [
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
                    'taxable',
                    'account_number',
                    'firstname',
                    'lastname',
                    'email',
                ]
            ]
        ]);

        // Optionally assert the content of the response
        $customers = Customer::all();
        $responseData = $response->json('data');

        $this->assertCount($customers->count(), $responseData);

        foreach ($customers as $index => $customer) {
            $this->assertEquals($customer->id, $responseData[$index]['id']);
            $this->assertEquals($customer->user_id, $responseData[$index]['user_id']);
            $this->assertEquals($customer->account_number, $responseData[$index]['account_number']);
            $this->assertEquals($customer->taxable, $responseData[$index]['taxable']);
            $this->assertEquals($customer->user->firstname, $responseData[$index]['firstname']);
            $this->assertEquals($customer->user->lastname, $responseData[$index]['lastname']);
            $this->assertEquals($customer->user->email, $responseData[$index]['email']);
            // Add more fields to compare if needed
        }
    }

    public function testGetListUnauthorized()
    {
        $response = $this->get('/api/customers');

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
        $response = $this->get('/api/customers', [
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

    public function testCreateCustomerSuccess()
    {
        $customerData = $this->getCustomerData();
        $response = $this->json('POST', '/api/customers', $customerData, ['Authorization' => 'test1']);
        $expectedData = [
            'firstname' => $customerData['firstname'],
            'lastname' => $customerData['lastname'],
            'email' => $customerData['email'],
            'phone_number' => $customerData['phone_number'],
            'role' => $customerData['role'],
            'address' => $customerData['address'],
            'city' => $customerData['city'],
            'province' => $customerData['province'],
            'country' => $customerData['country'],
            'postal_code' => $customerData['postal_code'],
            'comments' => $customerData['comments'],
            'account_number' => $customerData['account_number'],
        ];
        $response->assertStatus(201);
        $this->assertJsonResponse($response, $expectedData);
    }

    public function testCreateFailed()
    {
        $response = $this->post('/api/customers', $this->getInvalidData(), ['Authorization' => 'test1']);

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
        $response = $this->post('/api/customers', $this->getInvalidData(), ['Authorization' => 'salah']);

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
            "email" => "test3mail.com",
        ]);
    }

    public function testGetDetailUnauthorized()
    {
        $response = $this->get('/api/customers/3');

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
        $response = $this->get('/api/customers/3', [
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

    public function testUpdateFailed()
    {
        $response = $this->patch('/api/customers/3', [
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
