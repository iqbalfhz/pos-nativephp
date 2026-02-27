<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ActivityLog::with('user')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('url', 'like', "%{$search}%")
                    ->orWhere('route_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $query->paginate(25)->withQueryString()->through(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'Guest',
                'method' => $log->method,
                'route_name' => $log->route_name,
                'url' => $log->url,
                'ip_address' => $log->ip_address,
                'status_code' => $log->status_code,
                'payload' => $log->payload,
                'created_at' => $log->created_at->format('d M Y H:i:s'),
            ]),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $request->search,
                'method' => $request->method,
                'user_id' => $request->user_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
            'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        ]);
    }
}
