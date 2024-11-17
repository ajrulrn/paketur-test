<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyCollection;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyRepository;
    protected $userRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $keyword = $request->keyword ?? null;
        $direction = $request->direction;
        $perPage = $request->per_page ?? 10;
        $companies = $this->companyRepository->getWithPagination($keyword, $direction, $perPage);
        return new CompanyCollection($companies);
    }

    public function store(StoreCompanyRequest $request)
    {
        if (auth('api')->user()->role->name !== 'Super Admin') {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $companyPayload = $request->only(['name', 'email', 'phone']);
        $company = $this->companyRepository->create($companyPayload);
        $userPayload = [
            'name' => $request->manager_name,
            'email' => $request->manager_email,
            'password' => $request->manager_password,
            'company_id' => $company->id
        ];
        $this->userRepository->createManager($userPayload);
        return response()->json([
            'message' => 'company has been created'
        ], 201);
    }

    public function destroy($id)
    {
        if (auth('api')->user()->role->name !== 'Super Admin') {
            return response()->json([
                'message' => 'unauthorized access'
            ], 403);
        }

        $this->companyRepository->delete($id);
        return response()->json([
            'message' => 'company has been deleted'
        ]);
    }
}
