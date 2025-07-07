<?php

namespace App\Http\Props\Debugging;

use App\Http\Resources\Debugging\ActivityLogCollection;
use App\Models\Debugging\ActivityLog;
use App\Models\User;
use App\Support\UserAgent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ActivityLogProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => false,
            'read' => Auth::user()->can('read activity trace') || Auth::user()->hasRole(__('Superuser')),
            'update' => false,
            'delete' => false,
            'export' => Auth::user()->can('export activity traces') || Auth::user()->hasRole(__('Superuser')),
        ];
    }

    public static function index(): array
    {
        $filtersOnly = Request::only([
            'search',
            'sortBy',
        ]);
        $filtersAll = Request::all([
            'search',
            'sortBy',
        ]);

        $perPage = Request::input('per_page', 10);

        return [
            'can' => self::getPermissions(),
            'filters' => $filtersAll,
            'users' => Inertia::lazy(fn() => User::select(['id', 'name'])->get()),
            'events' => Inertia::lazy(fn() => [
                'created',
                'updated',
                'deleted',
                'authenticated',
                'authorized'
            ]),
            'logNames' => Inertia::lazy(fn() => [
                __('Security/Users'),
                __('Security/Permissions'),
                __('Security/Roles'),
                __('Organization/Organizations'),
                __('Organization/Administrative Units'),
                __('Authentication'),
            ]),
            'logs' => new ActivityLogCollection(
                ActivityLog::filter($filtersOnly)
                    ->select([
                        'activity_log.id',
                        'activity_log.description',
                        'activity_log.causer_id',
                        'activity_log.causer_type',
                        'activity_log.created_at',
                        'activity_log.updated_at',
                    ])
                    ->with('causer')
                    ->paginate($perPage)
                    ->withQueryString()
            ),
        ];
    }

    public static function show(ActivityLog $activityLog): array
    {
        return [
            'can' => self::getPermissions(),
            'log' => $activityLog->load(['causer', 'subject']),
            'userAgent' => [
                'details' => UserAgent::details($activityLog->properties->get('request')['user_agent']),
                'locale' => UserAgent::locale($activityLog->properties->get('request')['user_agent_lang'])
            ],
        ];
    }
}
