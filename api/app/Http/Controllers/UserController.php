<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest; // Add this line
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isSuperAdmin = $request->query('is_super_admin');
        $isAdmin = $request->query('is_admin');
        $company_name = $request->query('company_name');

        // Utilize the new method in UserService
        return $this->userService->getAllUsersWithCompanyData($isSuperAdmin, $isAdmin,  $company_name);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) // Change Request to StoreUserRequest
    {
        // Validation is now handled by StoreUserRequest
        $validatedData = $request->validated();

        try {
            $user = $this->userService->createUser($validatedData);
            // Load company and position information for the response if needed
            $user->load('companies'); // Example: load companies relationship
            return response()->json($user, 201);
        } catch (\Exception $e) {
            // Log error $e->getMessage()
            return response()->json(['message' => 'Erro ao criar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userService->getUserByIdWithCompany((int)$id);
            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado.'], 404);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            // Log error $e->getMessage()
            return response()->json(['message' => 'Erro ao buscar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $validatedData = $request->validated();

        try {
            $user = $this->userService->updateUser((int)$id, $validatedData);
            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado para atualizar.'], 404);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            // Log error $e->getMessage()
            return response()->json(['message' => 'Erro ao atualizar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->userService->deleteUser((int)$id);
            if ($deleted) {
                return response()->json(['message' => 'Usuário deletado com sucesso.'], 200);
            }
            return response()->json(['message' => 'Usuário não encontrado para deletar.'], 404);
        } catch (\Exception $e) {
            // Log error $e->getMessage()
            return response()->json(['message' => 'Erro ao deletar usuário.', 'error' => $e->getMessage()], 500);
        }
    }
}
