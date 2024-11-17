<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();
        $keyword = $request->keyword ?? null;
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;
        $companyId = in_array($user->role->name, ['Manager', 'Employee']) ? $user->company_id : null;
        $employees = $this->userRepository->getEmployeeWithPagination($keyword, $direction, $companyId, $perPage);
        return new EmployeeCollection($employees);
    }

    public function show($id)
    {
        $user = auth('api')->user();
        $employee = $this->userRepository->getEmployeeById($id);

        if (
            !$employee
            || (
                in_array($user->role->name, ['Manager', 'Employee'])
                && $employee->company_id !== $user->company_id
            )
            || $employee->role->name !== 'Employee'
        ) {
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return new EmployeeResource($employee);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $user = auth('api')->user();

        if ($user->role->name !== 'Manager') {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $data = $request->only(['name', 'email', 'password', 'phone', 'address']);
        $data['company_id'] = $user->company_id;
        $this->userRepository->createEmployee($data);
        return response()->json([
            'message' => 'employee has been created'
        ], 201);
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $user = auth('api')->user();

        if (
            (
                $user->role->name === 'Manager'
                && $user->company_id !== $employee->company_id
            )
            || (
                $user->role->name === 'Employee'
                && $user->id !== $employee->id
            )
        ) {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $data = $request->only(['name', 'email', 'phone', 'address']);
        $this->userRepository->updateEmployee($employee->id, $data);
        return response()->json([
            'message' => 'employee has been updated'
        ]);
    }

    public function destroy(User $employee)
    {
        $user = auth('api')->user();

        if (
            $user->role->name !== 'Manager'
            || $user->company_id !== $employee->company_id
        ) {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $this->userRepository->deleteEmployee($employee->id);
        return response()->json([
            'message' => 'employee has been deleted'
        ]);
    }
}
