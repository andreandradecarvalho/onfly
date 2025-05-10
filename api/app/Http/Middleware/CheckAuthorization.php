<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if Authorization header is present
            $authHeader = $request->header('Authorization');
            if (!$authHeader) {
                return new JsonResponse([
                    'error' => [
                        'message' => 'Authorization token is required',
                        'code' => 'UNAUTHORIZED'
                    ]
                ], 401);
            }

            // Check if the Authorization header starts with 'Bearer '
            if (!preg_match('/^Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return new JsonResponse([
                    'error' => [
                        'message' => 'Invalid authorization header format',
                        'code' => 'INVALID_HEADER'
                    ]
                ], 401);
            }

            // Attempt to validate the token
            $token = $matches[1];
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return new JsonResponse([
                    'error' => [
                        'message' => 'User not found',
                        'code' => 'USER_NOT_FOUND'
                    ]
                ], 401);
            }
        } catch (TokenInvalidException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Invalid token',
                    'code' => 'INVALID_TOKEN'
                ]
            ], 401);
        } catch (TokenExpiredException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Token has expired',
                    'code' => 'TOKEN_EXPIRED'
                ]
            ], 401);
        } catch (JWTException $e) {
            return new JsonResponse([
                'error' => [
                    'message' => 'Authorization error',
                    'code' => 'AUTHORIZATION_ERROR'
                ]
            ], 401);
        }

        return $next($request);
    }
}
