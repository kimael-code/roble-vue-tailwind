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
            'create' => Auth::user()->can('create new organizations'),
            'read' => Auth::user()->can('read organization'),
            'update' => Auth::user()->can('update organizations'),
            'delete' => Auth::user()->can('delete organizations'),
            'export' => Auth::user()->can('export organizations'),
        ];
    }

    public static function index(): array
    {
        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'sortBy',]),
            'organizations' => fn() => new OrganizationCollection(
                Organization::filter(Request::only(['search', 'sortBy',]))
                    ->paginate(10)
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
