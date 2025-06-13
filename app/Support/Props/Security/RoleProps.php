<?php

namespace App\Support\Props\Security;

use App\Http\Resources\Security\PermissionCollection;
use App\Http\Resources\Security\RoleCollection;
use App\Http\Resources\Security\UserCollection;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class RoleProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create new roles'),
            'read'   => Auth::user()->can('read role'),
            'update' => Auth::user()->can('update roles'),
            'delete' => Auth::user()->can('delete roles'),
            'export' => Auth::user()->can('export roles'),
        ];
    }

    public static function index(): array
    {
        return [
            'can'     => self::getPermissions(),
            'filters' => Request::all(['search', 'sortBy',]),
            'roles'   => fn() => new RoleCollection(
                Role::filter(Request::only(['search', 'sortBy',]))
                    ->paginate(10)
                    ->withQueryString()
            ),
        ];
    }

    public static function create(): array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 10);
        $permissions = Permission::filter(Request::only(['search']))
                                 ->paginate($perPage, page: $page);

        return [
            'permissions' => Inertia::merge(fn() => $permissions->items()),
            'pagination' => $permissions->toArray(),
            'filters' => Request::all(['search',]),
        ];
    }

    public static function show(Role $role): array
    {
        return [
            'can'         => Arr::except(self::getPermissions(), 'read'),
            'filters'     => Request::only(['search']),
            'permissions' => fn() => new PermissionCollection(
                $role->permissions()->filter(Request::only(['search']))
                    ->latest()
                    ->paginate(10, pageName: 'page_p')
            ),
            'role'        => $role,
            'users'       => fn() => new UserCollection(
                User::filter(Request::only(['search']))
                    ->role($role->name)
                    ->latest()
                    ->paginate(10, pageName: 'page_u')
            ),
        ];
    }

    public static function edit(Role $role): array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 10);
        $permissions = Permission::filter(Request::only(['search']))
                                 ->paginate($perPage, page: $page);
        return [
            'permissions' => Inertia::merge(fn() => $permissions->items()),
            'pagination' => $permissions->toArray(),
            'filters' => Request::all(['search',]),
            'role' => $role,
            'rolePermissions' => $role->permissions()->pluck('description')->all(),
        ];
    }
}

