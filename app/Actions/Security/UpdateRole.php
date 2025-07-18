<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UpdateRole
{
    private static bool $permissionsChanged = false;

    public static function handle(Role $role, array $inputs): void
    {
        DB::transaction(function () use ($inputs, $role)
        {
            $role->name        = $inputs['name'];
            $role->guard_name  = $inputs['guard_name'];
            $role->description = $inputs['description'];
            $role->save();

            $assignedPermissions = Permission::whereIn('description', $inputs['permissions'])->get();

            self::revokePermissions($role, $assignedPermissions);
            self::givePermissions($role, $assignedPermissions);
        });

        if (self::$permissionsChanged)
        {
            session()->flash('message', [
                'message' => "{$role->name} ({$role->description})",
                'title' => __('SAVED!'),
                'type'  => 'success',
            ]);
        }
    }

    public static function revokePermissions(Role $role, Collection $assignedPermissions): void
    {
        $authUser = auth()->user();

        foreach ($role->permissions as $permission)
        {
            if ($assignedPermissions->doesntContain($permission->name))
            {
                $role->revokePermissionTo($permission->name);

                activity(__('Security/Roles'))
                    ->causedBy($authUser)
                    ->performedOn($role)
                    ->event('authorized')
                    ->withProperties([
                        __('revoked_permission') => $permission,
                        __('from_role') => $role,
                        'causer' => User::with('person')->find($authUser->id)->toArray(),
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__(':username: revoked the [:permission] permission from the [:role] role', [
                        'username' => $authUser->name,
                        'permission' => $permission->description,
                        'role' => $role->name,
                    ]));
                self::$permissionsChanged = true;
            }
        }
    }

    public static function givePermissions(Role $role, Collection $assignedPermissions): void
    {
        $authUser = auth()->user();

        foreach ($assignedPermissions as $assignedPermission)
        {
            if ($role->permissions->doesntContain($assignedPermission->name))
            {
                $role->givePermissionTo($assignedPermission->name);

                activity(__('Security/Roles'))
                    ->causedBy($authUser)
                    ->performedOn($role)
                    ->event('authorized')
                    ->withProperties([
                        __('granted_permission') => $assignedPermission,
                        __('to_role') => $role,
                        'causer' => User::with('person')->find($authUser->id)->toArray(),
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__(':username: granted the [:permission] permission to role [:role]', [
                        'username' => $authUser->name,
                        'permission' => $assignedPermission->description,
                        'role' => $role->name,
                    ]));
                self::$permissionsChanged = true;
            }
        }
    }
}
