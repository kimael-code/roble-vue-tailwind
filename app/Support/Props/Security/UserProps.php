<?php

namespace App\Support\Props\Security;

use App\Http\Resources\Security\PermissionCollection;
use App\Http\Resources\Security\RoleCollection;
use App\Http\Resources\Security\UserCollection;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class UserProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create users'),
            'read' => Auth::user()->can('read user'),
            'update' => Auth::user()->can('update users'),
            'delete' => Auth::user()->can('delete users'),
            'export' => Auth::user()->can('export users'),
        ];
    }

    public static function index(): array
    {
        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'name', 'email']),
            'users' => fn() => new UserCollection(
                User::filter(Request::only(['search', 'name', 'email']))
                    ->paginate(10)
                    ->withQueryString()
            ),
        ];
    }

    public static function create(): array
    {
        $pagePerm = request()->input('page_perm', 1);
        $pageRole = request()->input('page_role', 1);
        $perPage = request()->input('per_page', 10);
        $permissions = Permission::filter(Request::only(['search']))
            ->paginate($perPage, page: $pagePerm);
        $roles = Role::filter(Request::only(['search']))
            ->paginate($perPage, page: $pageRole);
        $repository = new EmployeeRepository();

        return [
            'filters' => Request::all(['search',]),
            'employees' => Request::input('search') ? $repository->find(request()->input('search')) : [],
            'permissions' => Inertia::merge(fn() => $permissions->items()),
            'roles' => Inertia::merge(fn() => $roles->items()),
            'paginationPerm' => $permissions->toArray(),
            'paginationRole' => $roles->toArray(),
        ];
    }

    public static function show(User $user): array
    {
        $search = Request::only(['search']);
        $pagePerm = request()->input('page_perm', 1);
        $perPage = request()->input('per_page', 10);
        $permissions = new PermissionCollection($user->getAllPermissions()->filter(function ($permission) use ($search)
        {
            if (isset($search['search']))
            {
                // dd($search['search']);
                return str_contains($permission->description, $search['search']);
            }
            else
            {
                return $permission;
            }
        })->forPage($pagePerm, $perPage)->all());
        // dd($permissions);
        // $permissions = $permissions;

        return [
            'can' => Arr::except(self::getPermissions(), 'read'),
            'filters' =>  Request::all(['search']),
            'user' => $user,
            'pagePerm' => (int)$pagePerm,
            'permissions' => Inertia::deepMerge(fn () => $permissions),
            // 'permissions' => $permissions,
            'roles' => fn() => new RoleCollection(
                $user->roles()->filter($search)
                    ->latest()
                    ->paginate(10)
            ),
        ];
    }

    // public static function edit(Role $role): array
    // {
    //     $page = request()->input('page', 1);
    //     $perPage = request()->input('per_page', 10);
    //     $permissions = Permission::filter(Request::only(['search']))
    //                              ->paginate($perPage, page: $page);
    //     return [
    //         'permissions' => Inertia::merge(fn() => $permissions->items()),
    //         'pagination' => $permissions->toArray(),
    //         'filters' => Request::all(['search',]),
    //         'role' => $role,
    //         'rolePermissions' => $role->permissions()->pluck('description')->all(),
    //     ];
    // }
}

