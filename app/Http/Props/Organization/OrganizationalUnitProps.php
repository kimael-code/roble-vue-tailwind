<?php

namespace App\Http\Props\Organization;

use App\Http\Resources\Monitoring\ActivityLogCollection;
use App\Http\Resources\Organization\OrganizationalUnitCollection;
use App\Models\Monitoring\ActivityLog;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationalUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OrganizationalUnitProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create new organizational units'),
            'read' => Auth::user()->can('read organizational unit'),
            'update' => Auth::user()->can('update organizational units'),
            'delete' => Auth::user()->can('delete organizational units'),
            'export' => Auth::user()->can('export organizational units'),
        ];
    }

    public static function index(): array
    {
        $perPage = Request::input('per_page', 10);

        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'sort_by',]),
            'organizationalUnits' => new OrganizationalUnitCollection(
                OrganizationalUnit::filter(Request::only(['search', 'sort_by',]))
                    ->select([
                        'organizational_units.id',
                        'organizational_units.name',
                        'organizational_units.disabled_at',
                        'organization_id',
                    ])
                    ->with(['organization'])
                    ->paginate($perPage)
                    ->withQueryString()
            ),
        ];
    }

    public static function create(): array
    {
        return [
            'activeOrganizations' => Organization::active()->with(['activeOrganizationalUnits'])->get(),
        ];
    }

    public static function show(OrganizationalUnit $ou): array
    {
        return [
            'can' => Arr::except(self::getPermissions(), 'read'),
            'filters' => Request::all(['search']),
            'organizationalUnit' => $ou->load([
                'organization',
                'organizationalUnit',
            ]),
            'organizationalUnits' => fn() => new OrganizationalUnitCollection(
                $ou->organizationalUnits()->filter(Request::only(['search', 'name']))
                    ->latest()
                    ->paginate(10)
            ),
            'logs' => fn() => new ActivityLogCollection(
                ActivityLog::filter(Request::only(['search']))
                    ->whereHasMorph(
                        'subject',
                        OrganizationalUnit::class,
                        fn(Builder $query) => $query->where('id', $ou->id)
                    )
                    ->latest()
                    ->paginate(10, pageName: 'page_l')
                    ->withQueryString()
            ),
        ];
    }

    public static function edit(OrganizationalUnit $organizationalUnit): array
    {
        return [
            'organizationalUnits' => $organizationalUnit->organization->activeOrganizationalUnits,
            'organizationalUnit' => $organizationalUnit->load(['organization',]),
        ];
    }
}
