<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Models\UserRegistration;
use App\Repository\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserRegistrationController extends Controller
{
    public function register(UserRegistrationRequest $request, UserRepository $userRepository)
    {
       try {

        $user = $userRepository->create($request);

        return response()->json( ['data' => ['user' => $user]], Response::HTTP_CREATED);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

    }
}
