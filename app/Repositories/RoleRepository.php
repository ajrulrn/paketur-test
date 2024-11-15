<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    public function getByName($name)
    {
        return Role::where('name', $name)->first();
    }
}
