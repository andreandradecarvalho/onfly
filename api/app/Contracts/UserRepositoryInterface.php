<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function create(Request $request);
    
    /**
     * Get authenticated user with company information
     * 
     * @param int $userId
     * @return mixed
     */
    public function getUserWithCompany(int $userId);
}
