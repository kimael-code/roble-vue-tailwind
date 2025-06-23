<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateRole
{
    public static function handle(array $inputs): void
    {
        DB::transaction(function () use ($inputs)
        {
            $role = Role::create([
                'name' => $inputs['name'],
                'guard_name' => $inputs['guard_name'],
                'description' => $inputs['description'],
            ]);

            foreach ($inputs['permissions'] as $description)
            {
                $user = auth()->user();
                $permission = Permission::where('description', $description)
                    ->where('guard_name', $inputs['guard_name'])
                    ->first();

                $role->givePermissionTo($permission);

                activity(__('Security/Roles'))
                    ->causedBy($user)
                    ->performedOn($role)
                    ->event('authorized')
                    ->withProperties([
                        __('assigned_permission') => $permission,
                        __('to_role') => $role,
                        'causer' => User::with('person')->find($user->id)->toArray(),
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__(':username: assigned permission [:permission] to role [:role]', [
                        'username' => $user->name,
                        'permission' => $permission->description,
                        'role' => $role->name,
                    ]));
            }
        });
    }
}
