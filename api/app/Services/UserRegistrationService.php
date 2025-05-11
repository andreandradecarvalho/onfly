<?php

namespace Api\Service;

use App\Http\Requests\UserRegistrationRequest;
use App\Models\UserRegistration;
use App\Repositories\UserRepository;


class UserRegistrationService
{
    protected $employeeRepository;
    protected $contractorRepository;

    public function __construct(
        UserRepository $userRepository,
        UserRegistrationRequest  $userRegistrationRequest
    )
    {
        $this->userRepository = $userRepository;
        $this->userRegistrationRequest = $userRegistrationRequest;
    }

    public function register($r)
    {
        dd($r);

    }
}
