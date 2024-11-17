<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManagerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_managers(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        User::factory()->count(5)->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson('/api/managers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'company_id',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    public function test_manager_can_view_managers_with_search_and_pagination(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        User::factory()->count(25)->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson('/api/managers?keyword=john&direction=asc&per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure(['data','meta','links']);
    }

    public function test_manager_can_view_manager(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $anotherManager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/managers/{$anotherManager->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'company_id',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ]
            ]);
    }

    public function test_show_error_when_manager_not_found(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/managers/100");

        $response->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'data not found']);
    }

    public function test_show_error_when_unauthorized_access(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        User::factory()->count(5)->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $this->actingAs($employee, 'api');
        $response = $this->getJson("/api/managers");

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }
}
