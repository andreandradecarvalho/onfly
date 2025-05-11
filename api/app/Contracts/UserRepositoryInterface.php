<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function create(array $data);
    
    /**
     * Get authenticated user with company information
     * 
     * @param int $userId
     * @return mixed
     */
    public function getUserWithCompany(int $userId);

    public function getAllUsersWithCompany($isSuperAdminFilter = null, $isAdminFilter = null, $requestingUser = null);

    public function getUsersByCompanyIdWithCompany(int $companyId);

    public function delete(int $userId): bool;

    public function update(int $userId, array $data);
}
