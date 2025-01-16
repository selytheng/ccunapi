<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Illuminate\Support\Facades\Log;

class Authorization
{
    public function handle($request, Closure $next, ...$roles)
    {
        try {
            $user = FacadesJWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }
            $userRole = $user->role->name;

            // Clean up the roles array by trimming whitespace
            $roles = array_map('trim', $roles);

            if (!in_array($userRole, $roles)) {
                return response()->json([
                    'error' => 'Forbidden',
                    'userRole' => $userRole,
                    'allowedRoles' => $roles
                ], Response::HTTP_FORBIDDEN);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
