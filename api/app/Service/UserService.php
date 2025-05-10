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
}
