<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getEmployeeWithPagination($keyword = null, $direction = 'asc', $companyId = null, $perPage = 10)
    {
        $roleId = $this->getRoleIdByName('Employee');
        return User::when(isset($keyword), function (Builder $query) use ($keyword) {
                $query->where('name', 'ilike', "%{$keyword}%");
            })
            ->when(in_array($direction, ['asc', 'desc']), function (Builder $query) use ($direction) {
                $query->orderBy('name', $direction);
            }, function (Builder $query) {
                $query->orderBy('name');
            })
            ->when(isset($companyId), function (Builder $query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('role_id', $roleId)
            ->paginate($perPage);
    }

    public function getManagerWithPagination($keyword = null, $direction = 'asc', $companyId = null, $perPage = 10)
    {
        $roleId = $this->getRoleIdByName('Manager');
        return User::when(isset($keyword), function (Builder $query) use ($keyword) {
                $query->where('name', 'ilike', "%{$keyword}%");
            })
            ->when(in_array($direction, ['asc', 'desc']), function (Builder $query) use ($direction) {
                $query->orderBy('name', $direction);
            }, function (Builder $query) {
                $query->orderBy('name');
            })
            ->when(isset($companyId), function (Builder $query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('role_id', $roleId)
            ->paginate($perPage);
    }

    public function getEmployeeById($id)
    {
        $roleId = $this->getRoleIdByName('Employee');
        return User::where('id', $id)->where('role_id', $roleId)->first();
    }

    public function getManagerById($id)
    {
        $roleId = $this->getRoleIdByName('Manager');
        return User::where('id', $id)->where('role_id', $roleId)->first();
    }

    public function createManager($data)
    {
        $data['role_id'] = $this->getRoleIdByName('Manager');
        return $this->create($data);
    }

    public function createEmployee($data)
    {
        $data['role_id'] = $this->getRoleIdByName('Employee');
        return $this->create($data);
    }

    public function updateEmployee($id, $data)
    {
        $employee = $this->getEmployeeById($id);
        $employee->update($data);
    }

    public function deleteEmployee($id)
    {
        $this->getEmployeeById($id)->delete();
    }

    private function getRoleIdByName($name)
    {
        return $this->roleRepository->getByName($name)->id;
    }

    private function create($data)
    {
        return User::create($data);
    }
}
