<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticate_user_cannot_create_company(): void
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'ABC',
            'email' => 'example@abc.com',
            'phone' => '08123456789',
            'manager_name' => 'Goerge',
            'manager_email' => 'goerge@abc.com',
            'manager_password' => 'goerge@password'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthorized_user_cannot_create_company(): void
    {
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'password' => 'password'
        ]);
        $this->actingAs($manager, 'api');
        $response = $this->postJson('/api/companies', [
            'name' => 'ABC',
            'email' => 'example@abc.com',
            'phone' => '08123456789',
            'manager_name' => 'George',
            'manager_email' => 'george@abc.com',
            'manager_password' => 'george@password'
        ]);

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }

    public function test_unauthorized_user_cannot_delete_company(): void
    {
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'password' => 'password'
        ]);
        $company = Company::factory()->create();
        $this->actingAs($manager, 'api');
        $response = $this->delete("/api/companies/{$company->id}");

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }

    public function test_reject_create_company_if_data_is_invalid(): void
    {
        Role::factory()->count(3)->create();
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        $this->actingAs($superAdmin, 'api');
        $response = $this->postJson('/api/companies', [
            'name' => 'ABC',
            'phone' => '08123456789',
            'manager_name' => 'George',
            'manager_email' => 'george@abc.com',
            'manager_password' => 'george@password'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_super_admin_can_create_company(): void
    {
        Role::factory()->count(3)->create();
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        $this->actingAs($superAdmin, 'api');
        $response = $this->postJson('/api/companies', [
            'name' => 'ABC',
            'email' => 'example@abc.com',
            'phone' => '08123456789',
            'manager_name' => 'George',
            'manager_email' => 'george@abc.com',
            'manager_password' => 'george@password'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'company has been created']);

        $this->assertDatabaseHas('companies', [
                'name' => 'ABC',
                'email' => 'example@abc.com',
                'phone' => '08123456789'
            ])
            ->assertDatabaseHas('users', [
                'name' => 'George',
                'email' => 'george@abc.com'
            ]);
    }

    public function test_super_admin_can_delete_company(): void
    {
        $superAdminRole = Role::factory()->create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        $company = Company::factory()->create();
        $this->actingAs($superAdmin, 'api');
        $response = $this->delete("/api/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'company has been deleted']);
    }

    public function test_list_companies()
    {
        $superAdminRole = Role::factory()->create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        Company::factory()->count(25)->create();
        $this->actingAs($superAdmin, 'api');
        $response = $this->getJson("/api/companies");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    public function test_list_companies_with_pagination_and_search()
    {
        $superAdminRole = Role::factory()->create(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'password' => 'password'
        ]);
        Company::factory()->count(25)->create();
        $this->actingAs($superAdmin, 'api');
        $response = $this->getJson("/api/companies?keyword=test&direction=asc&per_page=5");

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links']);
    }
}
