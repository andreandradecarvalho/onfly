<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $adminUser;
    private $token;
    private $urlApi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_super_admin' => true
        ]);

        // Login and get token
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->token = $response->json('data.token');
        $this->urlApi = '/api/v1/companies';
    }

#[Test]
    public function admin_can_create_company()
    {
        $companyData = [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'document' => $this->faker->numerify('##.###.###/####-##'),
            'address' => $this->faker->address(),
            'responsibility' => $this->faker->word()
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson($this->urlApi, $companyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'document',
                    'address',
                    'responsibility',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    #[Test]
    public function admin_can_update_company()
    {

        $companyId =  $this->createCompany();

        // Update the company
        $updateData = [
            'name' => 'Updated ' . $this->faker->company(),
            'email' => 'updated_' . $this->faker->companyEmail(),
            'document' => $this->faker->numerify('##.###.###/####-##'),
            'address' => $this->faker->address(),
            'responsibility' => $this->faker->word()
        ];
        // Make sure we're using JWT token for update
        $updateResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->putJson("{$this->urlApi}/{$companyId}", $updateData);


        // For debugging
        if ($updateResponse->status() !== 200) {
            dump([
                'status' => $updateResponse->status(),
                'content' => $updateResponse->json(),
                'company_id' => $companyId,
                'token' => $this->token
            ]);
        }

        $updateResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'document',
                    'address',
                    'responsibility',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonFragment($updateData);

        // Verify the update in database
        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => $updateData['name'],
            'email' => $updateData['email']
        ]);
    }


  #[Test]
    public function admin_can_delete_company()
    {
        $companyId = $this->createCompany();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson("{$this->urlApi}/{$companyId}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('companies', ['id' => $companyId]);
    }

   #[Test]
    public function admin_can_view_company_details()
{

    $companyId =  $this->createCompany();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
        'Accept' => 'application/json'
    ])->getJson("{$this->urlApi}/{$companyId}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'document',
                'address',
                'responsibility',
                'created_at',
                'updated_at'
            ]
        ]);

    // Verify the company exists in database
    $this->assertDatabaseHas('companies', [
        'id' => $companyId,
        'name' => $response['data']['name'],
        'email' => $response['data']['email']
    ]);
}


   #[Test]
    public function admin_can_list_companies()
{
        $this->createCompany();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
        'Accept' => 'application/json'
    ])->getJson($this->urlApi);


   $response->assertStatus(200)
   ->assertJsonStructure([
       'data' => [
           '*' => [
               'id',
               'name',
               'document',
               'address',
               'email',
               'responsibility',
               'created_at',
               'updated_at'
           ]
       ]
   ]);

$this->assertCount(1, $response->json('data'));
}

public function createCompany(){
    $companyData = [
        'name' => $this->faker->company(),
        'email' => $this->faker->companyEmail(),
        'document' => $this->faker->numerify('##.###.###/####-##'),
        'address' => $this->faker->address(),
        'responsibility' => $this->faker->word()
    ];

    $createResponse = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token,
        'Accept' => 'application/json'
    ])->postJson($this->urlApi, $companyData);

    $createResponse->assertStatus(201);

    return $createResponse->json('data.id');
}

}
