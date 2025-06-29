<?php

namespace App\Http\Props\Organization;

use App\Http\Resources\Debugging\ActivityLogCollection;
use App\Http\Resources\Organization\OrganizationalUnitCollection;
use App\Http\Resources\Organization\OrganizationCollection;
use App\Models\Debugging\ActivityLog;
use App\Models\Organization\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OrganizationProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create new organizations') || Auth::user()->hasRole(__('Superuser')),
            'read' => Auth::user()->can('read organization') || Auth::user()->hasRole(__('Superuser')),
            'update' => Auth::user()->can('update organizations') || Auth::user()->hasRole(__('Superuser')),
            'delete' => Auth::user()->can('delete organizations') || Auth::user()->hasRole(__('Superuser')),
            'export' => Auth::user()->can('export organizations') || Auth::user()->hasRole(__('Superuser')),
        ];
    }

    public static function index(): array
    {
        $perPage = Request::input('per_page', 10);

        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'sortBy',]),
            'organizations' => fn() => new OrganizationCollection(
                Organization::filter(Request::only(['search', 'sortBy',]))
                    ->paginate($perPage)
                    ->withQueryString()
            ),
        ];
    }

    public static function show(Organization $organization): array
    {
        return [
            'can' => Arr::except(self::getPermissions(), 'read'),
            'filters' => Request::all(['search', 'name']),
            'organization' => $organization,
            'ous' => fn() => new OrganizationalUnitCollection(
                $organization->organizationalUnits()->filter(Request::only(['search', 'name']))
                    ->latest()
                    ->paginate(10)
            ),
            'logs' => fn() => new ActivityLogCollection(
                ActivityLog::filter(Request::only(['search']))
                    ->whereHasMorph(
                        'subject',
                        Organization::class,
                        fn(Builder $query) => $query->where('id', $organization->id)
                    )
                    ->latest()
                    ->paginate(10, pageName: 'page_l')
                    ->withQueryString()
            ),
        ];
    }

    public static function edit(Organization $organization): array
    {
        return ['organization' => $organization];
    }
}
