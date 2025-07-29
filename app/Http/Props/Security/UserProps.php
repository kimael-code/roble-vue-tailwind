<?php

namespace App\Http\Props\Security;

use App\Http\Resources\Debugging\ActivityLogCollection;
use App\Http\Resources\Security\PermissionCollection;
use App\Http\Resources\Security\RoleCollection;
use App\Http\Resources\Security\UserCollection;
use App\Models\Debugging\ActivityLog;
use App\Models\Organization\OrganizationalUnit;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class UserProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create new users') || Auth::user()->hasRole(__('Superuser')),
            'read' => Auth::user()->can('read user') || Auth::user()->hasRole(__('Superuser')),
            'update' => Auth::user()->can('update users') || Auth::user()->hasRole(__('Superuser')),
            'delete' => Auth::user()->can('delete users') || Auth::user()->hasRole(__('Superuser')),
            'f_delete' => Auth::user()->can('force delete users') || Auth::user()->hasRole(__('Superuser')),
            'export' => Auth::user()->can('export users') || Auth::user()->hasRole(__('Superuser')),
            'restore' => Auth::user()->can('restore users') || Auth::user()->hasRole(__('Superuser')),
            'activate' => Auth::user()->can('activate users') || Auth::user()->hasRole(__('Superuser')),
            'deactivate' => Auth::user()->can('deactivate users') || Auth::user()->hasRole(__('Superuser')),
        ];
    }

    public static function index(): array
    {
        $perPage = Request::input('per_page', 10);

        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['permissions', 'roles', 'statuses', 'search', 'sortBy',]),
            'permissions' => Inertia::lazy(
                fn() => Permission::filter(Request::only(['search',]))
                    ->select(['id', 'name', 'description'])
                    ->get()
            ),
            'roles' => Inertia::lazy(
                fn() => Role::filter(Request::only(['search',]))
                    ->select(['id', 'name',])
                    ->get()
            ),
            'users' => fn() => new UserCollection(
                User::withTrashed()->filter(Request::only(['permissions', 'roles', 'statuses', 'search', 'sortBy',]))
                    ->paginate($perPage)
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
        $roles = Role::filter(Request::only(['search']))
            ->paginate($perPage, page: $page);
        $ous = OrganizationalUnit::active()->filter(Request::only(['search']))
            ->paginate($perPage, page: $page);
        $repository = new EmployeeRepository();

        return [
            'filters' => Request::all(['search',]),
            'employees' => Request::input('search') ? $repository->find(request()->input('search')) : [],
            'ous' => Inertia::merge(fn() => $ous->items()),
            'permissions' => Inertia::merge(fn() => $permissions->items()),
            'roles' => Inertia::merge(fn() => $roles->items()),
            'paginationOu' => $ous->toArray(),
            'paginationPerm' => $permissions->toArray(),
            'paginationRole' => $roles->toArray(),
        ];
    }

    public static function show(User $user): array
    {
        $search = Request::only(['search']);
        $permissions = new PermissionCollection($user->getAllPermissions()->filter(function ($permission) use ($search)
        {
            if (isset($search['search']))
            {
                return str_contains($permission->description, $search['search']);
            }
            else
            {
                return $permission;
            }
        })->all());

        return [
            'can' => Arr::except(self::getPermissions(), 'read'),
            'filters' =>  Request::all(['search']),
            'user' => $user->load(['person', 'activeOrganizationalUnits']),
            'permissions' => fn () => $permissions,
            'permissionsCount' => fn () => $user->getAllPermissions()->count(),
            'roles' => fn() => new RoleCollection(
                $user->roles()->filter($search)
                    ->latest()
                    ->paginate(10)
            ),
            'logs' => fn() => new ActivityLogCollection(
                ActivityLog::filter(Request::only(['search']))
                    ->whereHasMorph(
                        'subject',
                        User::class,
                        fn(Builder $query) => $query->where('id', $user->id)
                    )
                    ->latest()
                    ->paginate(10, pageName: 'page_l')
                    ->withQueryString()
            ),
        ];
    }

    public static function edit(User $user): array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 10);

        $permissions = Permission::filter(Request::only(['search']))
            ->paginate($perPage, page: $page);
        $roles = Role::filter(Request::only(['search']))
            ->paginate($perPage, page: $page);
        $ous = OrganizationalUnit::active()->filter(Request::only(['search']))
            ->paginate($perPage, page: $page);
        $repository = new EmployeeRepository();

        return [
            'filters' => Request::all(['search',]),
            'user' => $user->load(['roles', 'permissions', 'person', 'activeOrganizationalUnits']),
            'employees' => Request::input('search') ? $repository->find(request()->input('search')) : [],
            'ous' => Inertia::merge(fn() => $ous->items()),
            'permissions' => Inertia::merge(fn() => $permissions->items()),
            'roles' => Inertia::merge(fn() => $roles->items()),
            'paginationOu' => $ous->toArray(),
            'paginationPerm' => $permissions->toArray(),
            'paginationRole' => $roles->toArray(),
        ];
    }
}

