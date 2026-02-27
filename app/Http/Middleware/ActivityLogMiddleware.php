<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!Schema::hasTable('activity_logs')) {
            return $response;
        }

        $payload = $request->isMethod('GET')
            ? $request->query()
            : $request->except(['password', 'password_confirmation', '_token']);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->getStatusCode(),
            'payload' => empty($payload) ? null : $payload,
        ]);

        return $response;
    }
}
