<?php

namespace App\Support\Props\Organization;

use App\Http\Resources\Organization\OrganizationalUnitCollection;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationalUnit;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OrganizationalUnitProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create organizational units'),
            'read' => Auth::user()->can('read organizational unit'),
            'update' => Auth::user()->can('update organizational units'),
            'delete' => Auth::user()->can('delete organizational units'),
            'export' => Auth::user()->can('export organizational units'),
        ];
    }

    public static function index(): array
    {
        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'name', 'organization', 'disabled_at']),
            'organizationalUnits' => fn() => new OrganizationalUnitCollection(
                OrganizationalUnit::filter(Request::only(['search', 'name', 'organization', 'disabled_at']))
                    ->with(['organization'])
                    ->paginate(10)
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
