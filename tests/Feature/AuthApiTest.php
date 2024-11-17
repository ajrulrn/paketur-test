<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid@paketur.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'invalid email or password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $superAdminRole = Role::factory()->create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        $response = $this->postJson('/api/auth/login', [
            'email' => $superAdmin->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ])
            ->assertJson([
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]);
    }
}
