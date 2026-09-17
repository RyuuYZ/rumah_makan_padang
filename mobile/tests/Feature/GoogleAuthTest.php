<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_creates_new_user_and_returns_session_data(): void
    {
        $payload = [
            'email' => 'customer.google@gmail.com',
            'name' => 'Siti Google',
            'avatar_url' => 'https://lh3.googleusercontent.com/a/photo.jpg',
            'google_id' => '1029384756',
        ];

        $response = $this->postJson('/api/v1/auth/google', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data' => [
                'email' => 'customer.google@gmail.com',
                'name' => 'Siti Google',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'customer.google@gmail.com',
            'name' => 'Siti Google',
            'role' => 'customer',
        ]);
    }

    public function test_google_login_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/google', [
            'name' => 'Invalid User',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
