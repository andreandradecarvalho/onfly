<?php

namespace App\Http\Controllers;

use App\Service\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private $companyService;

    /**
     * CompanyController constructor.
     *
     * @param CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Get companies based on user role
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Check if user is a SuperAdmin
        $isSuperAdmin = $user->isSuperAdmin();

        // Get companies based on user role
        $companies = $this->companyService->getCompaniesByUserRole($user, $isSuperAdmin);

        return response()->json([
            'data' => $companies,
            'is_super_admin' => $isSuperAdmin
        ]);
    }
}
