<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;
use App\Repository\UserRepository;

class UserService
{
    private $userRepository;

    /**
     * UserService constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

        return $this->userRepository->getUserWithCompany($user->id);
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

        return $this->userRepository->create($data);
    }

    /**
     * Retrieve a specific user by ID with company information.
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserByIdWithCompany(int $userId)
    {
        return $this->userRepository->getUserWithCompany($userId);
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
        return $this->userRepository->update($userId, $data);
    }

    /**
     * Delete a user by ID.
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
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
            return $this->userRepository->getAllUsers();
        }

        if ($user->is_admin) {
            return $this->userRepository->getUsersByCompanyId($user->company_id);
        }

        // Assuming getUserById also fetches company or you have a specific method for it
        return $this->userRepository->getUserWithCompany($user->id);

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

        // If the current user is not an admin or super_admin, and no specific filters are applied that would grant them wider access,
        // they should only see their own data. This also handles the case for regular users.
        // However, the primary logic for filtering (including who can see what based on filters) is now in the repository.
        // The service layer ensures the authenticated user context is passed.

        // If specific filters are provided, pass them to the repository.
        // If no filters are provided (e.g., a general /users call), the repository will use the $currentUser's role
        // to determine the default scope (all for super_admin, company for admin, self for regular user - though self is handled if not admin/super_admin here).

        // For a regular user (not admin, not super_admin) trying to list all users without specific admin/super_admin filters:
        // This scenario should ideally not grant them a list of all users unless a specific filter allows it.
        // The repository logic needs to be robust. For now, let's check if they are an admin or super_admin.
        if (!$currentUser->is_admin && !$currentUser->is_super_admin) {
            // If query parameters for is_admin or is_super_admin are present, let the repository handle it.
            // Otherwise, a regular user defaults to seeing only themselves.
            if ($isSuperAdminFilter === null && $isAdminFilter === null) {
                 return $this->userRepository->getUserWithCompany($currentUser->id);
            }
        }

        // Admins, Super Admins, or calls with explicit role filters go to the repository's main listing method.
        return $this->userRepository->getAllUsersWithCompany($isSuperAdminFilter, $isAdminFilter, $currentUser, $company_name);
    }
}
