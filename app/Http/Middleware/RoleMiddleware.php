<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Parse roles string (supports "role1|role2" format)
        $roleArray = explode('|', $roles);

        // Check if user has any of the roles
        $hasRole = false;
        foreach ($roleArray as $role) {
            if ($user->hasRole(trim($role))) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            abort(403);
        }

        return $next($request);
    }
}
