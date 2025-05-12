<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


class UsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $baseUrl = '/api/users';
    private User $user;
    private User $admin;
    private User $superAdmin;

    private $token;
    private $urlApi;
    private $email;
    private $password;

    protected function setUp(): void
    {
        parent::setUp();

        $mail = $this->faker->companyEmail();
        $pwd = $this->faker->password();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => $mail,
            'password' => bcrypt($pwd),
            'is_admin' => true,
            'is_super_admin' => true
        ]);

        // Login and get token
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $mail,
            'password' => $pwd,
        ]);

        $response->assertStatus(200);
        $this->token = $response->json('data.token');
        $this->urlApi = '/api/v1/users';
        $this->email = $mail;
        $this->password = $pwd;
    }

   #[Test]
    public function it_can_register_new_user()
    {
        $companyId = $this->createCompany();
        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->companyEmail(),
            'password' => $this->faker->password(),
            'position_company_id' => null,
            'company_id' => $companyId
        ];


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson($this->urlApi, $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email']);

        $this->assertDatabaseHas('users', [
            'email' => $response['email'],
            'name' => $response['name']
        ]);
    }

   #[Test]
    public function it_can_update_user_profile()
    {
        $newData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail
        ];

        $response = $this->actingAs($this->user)
            ->putJson("{$this->baseUrl}/{$this->user->id}", $newData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => $newData['name'],
                'email' => $newData['email']
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $newData['name'],
            'email' => $newData['email']
        ]);
    }

   #[Test]
    public function super_admin_can_list_all_users()
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson($this->baseUrl);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta'
            ]);
    }

   #[Test]
    public function normal_user_cannot_list_all_users()
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->baseUrl);

        $response->assertStatus(403);
    }

   #[Test]
    public function super_admin_can_delete_user()
    {
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson("{$this->baseUrl}/{$userToDelete->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

   #[Test]
    public function it_validates_registration_data()
    {
        $response = $this->postJson("{$this->baseUrl}/register", [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

   #[Test]
    public function it_can_attach_user_to_company()
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->postJson("{$this->baseUrl}/{$this->user->id}/companies", [
                'company_id' => $company->id
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('company_user', [
            'user_id' => $this->user->id,
            'company_id' => $company->id
        ]);
    }

   #[Test]
    public function it_can_get_user_companies()
    {
        $companies = Company::factory()->count(3)->create();
        $this->user->companies()->attach($companies->pluck('id'));

        $response = $this->actingAs($this->user)
            ->getJson("{$this->baseUrl}/{$this->user->id}/companies");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
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
        ])->postJson('/api/v1/companies', $companyData);

        $createResponse->assertStatus(201);

        return $createResponse->json('data.id');
    }


    public function posiotionCompany(){
        return 1;
    }

    public function createUser(){
        $companyId = $this->createCompany();

        $name = $this->faker->name();
        $email = $this->faker->companyEmail();
        $password = $this->faker->password();

        $userData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->companyEmail(),
            'password' => $this->faker->password(),
            'position_company_id' => null,
            'company_id' => $companyId
        ];


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson($this->urlApi, $userData);

        return [
            'id' =>  $response->json('id'),
            'name' => $name ,
            'email' => $email,
            'password' => $password,
        ];
    }
}