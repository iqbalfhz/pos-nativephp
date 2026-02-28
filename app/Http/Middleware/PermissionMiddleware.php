<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Parse permissions string (supports "permission1|permission2" format)
        $permissionArray = explode('|', $permissions);

        // Check if user has any of the permissions
        $hasPermission = false;
        foreach ($permissionArray as $permission) {
            if ($user->hasPermissionTo(trim($permission))) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403);
        }

        return $next($request);
    }
}
