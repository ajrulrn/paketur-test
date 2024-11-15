<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManagerCollection;
use App\Http\Resources\ManagerResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();

        if (!in_array($user->role->name, ['Super Admin', 'Manager'])) {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $keyword = $request->keyword ?? null;
        $direction = $request->direction ?? 'asc';
        $perPage = $request->per_page ?? 10;
        $companyId = $user->role->name === 'Manager' ? $user->company_id : null;
        $employees = $this->userRepository->getManagerWithPagination($keyword, $direction, $companyId, $perPage);
        return new ManagerCollection($employees);
    }

    public function show($id)
    {
        $user = auth('api')->user();
        $manager = $this->userRepository->getManagerById($id);

        if (
            !$manager
            || (
                $user->role->name === 'Manager'
                && $manager->company_id !== $user->company_id
            )
        ) {
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return new ManagerResource($manager);
    }
}
