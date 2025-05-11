<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

class UserService
{
    private $repository;

    /**
     * UserService constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve the authenticated user with company information
     *
     * @return mixed
     */
    public function getUserWithCompany()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return $this->repository->getUserWithCompany($user->id);
    }

    /**
     * Create a new user and associate with company and position.
     *
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data)
    {
        // Basic validation can be done here or in the controller
        // For example, check if required fields like name, email, password, company_id are present

        // You might want to add more complex validation or business logic here,
        // e.g., check if company_id exists, check if position_id is valid for the company, etc.

        return $this->repository->create($data);
    }

    /**
     * Retrieve a specific user by ID with company information.
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserByIdWithCompany(int $userId)
    {
        return $this->repository->getUserWithCompany($userId);
    }

    /**
     * Update an existing user.
     *
     * @param int $userId
     * @param array $data
     * @return mixed
     */
    public function updateUser(int $userId, array $data)
    {
        return $this->repository->update($userId, $data);
    }

    /**
     * Delete a user by ID.
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        return $this->repository->delete($userId);
    }

    /**
     * Retrieve users based on the authenticated user's role.
     *
     * @return mixed
     */
    public function getUsersList()
    {
        $user = Auth::user();

        if (!$user) {
            // Or handle as an error, depending on application flow
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->is_super_admin) {
            return $this->repository->getAllUsers();
        }

        if ($user->is_admin) {
            return $this->repository->getUsersByCompanyId($user->company_id);
        }

        // O getUserWithCompany traz a empresa
        return $this->repository->getUserWithCompany($user->id);

    }

    /**
     * Retrieve users with their company data based on the authenticated user's role.
     *
     * @return mixed
     */
    public function getAllUsersWithCompanyData($isSuperAdminFilter = null, $isAdminFilter = null,  $company_name =null)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            // Or handle as an error, depending on application flow
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if (!$currentUser->is_admin && !$currentUser->is_super_admin) {

             if ($isSuperAdminFilter === null && $isAdminFilter === null) {
                 return $this->repository->getUserWithCompany($currentUser->id);
            }
        }

        return $this->repository->getAllUsersWithCompany($isSuperAdminFilter, $isAdminFilter, $currentUser, $company_name);
    }
}
