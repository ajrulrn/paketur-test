<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_error_when_employee_not_found(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'company_id' => $company->id
        ]);
        User::factory()->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id
        ]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/employees/100");
        $response->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'data not found']);
    }

    public function test_unauthorized_user_cannot_create_employee(): void
    {
        $company = Company::factory()->create();
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $this->actingAs($employee, 'api');
        $payload = [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'password' => 'tes@gmail.com',
            'phone' => '123345677889',
            'address' => 'Jl.'
        ];
        $response = $this->postJson('/api/employees', $payload);

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }

    public function test_unauthorized_user_cannot_update_employee(): void
    {
        $company = Company::factory()->create();
        $anotherCompany = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $anotherCompany->id]);
        $this->actingAs($manager, 'api');
        $payload = [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'phone' => '123345677889',
            'address' => 'Jl.'
        ];
        $response = $this->putJson("/api/employees/{$employee->id}", $payload);

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }

    public function test_unauthorized_user_cannot_delete_employee(): void
    {
        $company = Company::factory()->create();
        $anotherCompany = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $anotherCompany->id]);
        $this->actingAs($manager, 'api');
        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'unauthorized access']);
    }

    public function test_manager_can_view_employees(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'company_id' => $company->id
        ]);
        User::factory()->count(5)->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id
        ]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/employees");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'company_id',
                        'phone',
                        'address',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    public function test_manager_user_can_view_employees_with_pagination_and_search(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'company_id' => $company->id
        ]);
        User::factory()->count(25)->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id
        ]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/employees?keyword=john&direction=asc&per_page=5");
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_reject_create_employee_if_data_is_invalid(): void
    {
        Role::factory()->count(3)->create();
        $company = Company::factory()->create();
        $managerRole = Role::where('name', 'Manager')->first();
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->postJson('/api/employees', [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'password' => 'tes@gmail.com',
            'phone' => '123345677889',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_manager_can_create_employee(): void
    {
        Role::factory()->count(3)->create();
        $company = Company::factory()->create();
        $managerRole = Role::where('name', 'Manager')->first();
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $payload = [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'password' => 'tes@gmail.com',
            'phone' => '123345677889',
            'address' => 'Jl.'
        ];
        $response = $this->postJson('/api/employees', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'employee has been created']);

        unset($payload['password']);
        $this->assertDatabaseHas('users', $payload);
    }

    public function test_manager_can_view_employee(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $manager = User::factory()->create([
            'role_id' => $managerRole->id,
            'company_id' => $company->id
        ]);
        $employee = User::factory()->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id
        ]);
        $this->actingAs($manager, 'api');
        $response = $this->getJson("/api/employees/{$employee->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'company_id',
                    'phone',
                    'address',
                    'created_at',
                    'updated_at',
                    'deleted_at'
                ]
            ]);
    }

    public function test_employee_can_view_fellow_employees(): void
    {
        $company = Company::factory()->create();
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create([
            'role_id' => $employeeRole->id,
            'company_id' => $company->id
        ]);
        User::factory()->count(5)->create([
            'role_id' => $employee->id,
            'company_id' => $company->id
        ]);

        $this->actingAs($employee, 'api');
        $response = $this->getJson('/api/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'company_id',
                        'phone',
                        'address',
                        'created_at',
                        'updated_at',
                        'deleted_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    public function test_reject_update_employee_if_data_is_invalid(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $payload = [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'phone' => '123345677889'
        ];
        $response = $this->putJson("/api/employees/{$employee->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_manager_can_update_employee(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $payload = [
            'name' => 'New Employee',
            'email' => 'tes@gmail.com',
            'phone' => '123345677889',
            'address' => 'Jl.'
        ];
        $response = $this->putJson("/api/employees/{$employee->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'employee has been updated']);
    }

    public function test_manager_can_delete_employee(): void
    {
        $company = Company::factory()->create();
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $employeeRole = Role::factory()->create(['name' => 'Employee']);
        $employee = User::factory()->create(['role_id' => $employeeRole->id, 'company_id' => $company->id]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'company_id' => $company->id]);
        $this->actingAs($manager, 'api');
        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'employee has been deleted']);
    }
}
