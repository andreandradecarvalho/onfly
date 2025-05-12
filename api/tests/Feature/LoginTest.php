<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User; // Ajuste conforme o modelo do seu usuário
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    #[Test]
    public function user_can_login_with_valid_credentials()
    {

        $email = $this->faker->unique()->safeEmail();

        // Criar um usuário para o teste
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('123456'),
        ]);

        // Enviar a requisição de login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => '123456',
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'token',
                'token_type',
                'expires_in',
                'user'
            ]
        ]);
        $responseData = $response->json();
        $this->assertEquals('bearer', $responseData['data']['token_type']);
        $this->assertEquals(3600, $responseData['data']['expires_in']);
        $this->assertNotEmpty($responseData['data']['token']);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials()
    {

        $email = $this->faker->unique()->safeEmail();

        // Criar um usuário para o teste
        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('123456'),
        ]);

        // Enviar a requisição de login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'sdfsdfer',
        ]);

        $response->assertStatus(401);
        $responseData = $response->json();
    }

    #[Test]
    public function login_fails_with_missing_fields()
    {

        $email = $this->faker->unique()->safeEmail();

        // Criar um usuário para o teste
        $user = User::factory()->create([
            'email' => $email,
            'password' => '',
        ]);

        // Enviar a requisição de login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => 'sdfsdfer',
        ]);

        $response->assertStatus(401);
        $responseData = $response->json();
    }
}
