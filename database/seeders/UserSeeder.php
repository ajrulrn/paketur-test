<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'Super Admin')->first();
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@paketur.com',
            'password' => 'paketur@password',
            'role_id' => $role->id
        ]);
    }
}
