<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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


    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $company = $this->companyService->createCompany($validatedData);

            return response()->json([
                'message' => 'Empresa criada com sucesso',
                'data' => $company
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->companyService->deleteCompany($id);
            return response()->json(['message' => 'Empresa deletada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(int $id, UpdateCompanyRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $company = $this->companyService->updateCompany($id, $validatedData);

            return response()->json([
                'message' => 'Empresa atualizada com sucesso',
                'data' => $company
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyById($id);
            return response()->json([
                'message' => 'Empresa encontrada com sucesso',
                'data' => $company
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
