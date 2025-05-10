<?php

namespace App\Support\Props\Organization;

use App\Http\Resources\Organization\OrganizationalUnitCollection;
use App\Http\Resources\Organization\OrganizationCollection;
use App\Models\Organization\Organization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OrganizationProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create organizations'),
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
            'filters' => Request::all(['search', 'name', 'rif', 'acronym']),
            'organizations' => fn() => new OrganizationCollection(
                Organization::filter(Request::only(['search', 'name', 'rif', 'acronym']))
                    ->paginate(10)
                    ->withQueryString()
            ),
        ];
    }

    public static function show(Organization $organization): array
    {
        return [
            'can' => Arr::except(self::getPermissions(), 'read'),
            'filters' => Request::only(['search', 'name']),
            'organization' => $organization,
            'ous' => fn() => new OrganizationalUnitCollection(
                $organization->organizationalUnits()->filter(Request::only(['search', 'name']))
                    ->latest()
                    ->paginate(10)
            ),
        ];
    }

    public static function edit(Organization $organization): array
    {
        return ['organization' => $organization];
    }
}
