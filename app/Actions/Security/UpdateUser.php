<?php

namespace App\Actions\Security;

use App\Models\Organization\OrganizationalUnit;
use App\Models\Person;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use App\Notifications\ActionHandledOnModel;
use Illuminate\Support\Facades\DB;

class UpdateUser
{
    protected static $notify = false;

    public static function handle(User $user, array $inputs): User
    {
        DB::transaction(function () use ($inputs, &$user)
        {
            $user->name = $inputs['name'];
            $user->email = $inputs['email'];
            $user->is_external = $inputs['is_external'];

            if ($user->isDirty())
            {
                self::$notify = true;
            }

            $user->save();

            self::removeRoles($user, $inputs['roles']);
            self::assignRoles($user, $inputs['roles']);
            self::revokePermissions($user, $inputs['permissions']);
            self::givePermissions($user, $inputs['permissions']);
            self::setPerson($user, $inputs);

            if (self::$notify)
            {
                session()->flash('message', [
                    'message' => "{$user->name}",
                    'title' => __('SAVED!'),
                    'type' => 'success',
                ]);

                $users = User::permission('update users')->get()->filter(
                    fn(User $user) => $user->id != auth()->user()->id
                )->all();

                foreach ($users as $user)
                {
                    $user->notify(new ActionHandledOnModel(
                        auth()->user(),
                        [
                            'id' => $user->id,
                            'type' => __('user'),
                            'name' => "({$user->name})",
                            'timestamp' => $user->updated_at,
                        ],
                        'updated',
                        ['routeName' => 'users', 'routeParam' => 'user']
                    ));
                }
            }
        });

        return $user;
    }

    private static function removeRoles(User $user, array $roleNames): void
    {
        $authUser = auth()->user();

        foreach ($user->roles as $role)
        {
            if (!in_array($role->name, $roleNames, true))
            {
                $user->removeRole($role->id);

                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('deleted')
                    ->withProperties([
                        __('unassigned_role') => $role,
                        __('to_user') => $user,
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
                    ->log(__(':username: unassigned the [:role] role to user [:user]', [
                        'username' => $authUser->name,
                        'role' => $role->name,
                        'user' => $user->name,
                    ]));

                self::$notify = true;
            }
        }
    }

    private static function assignRoles(User $user, array $roleNames): void
    {
        $authUser = auth()->user();

        foreach ($roleNames as $roleName)
        {
            $role = Role::findByName($roleName);

            if (!$user->hasRole($role->name))
            {
                $user->assignRole($role);

                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('created')
                    ->withProperties([
                        __('assigned_role') => $role,
                        __('to_user') => $user,
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
                    ->log(__(':username: assigned the [:role] role to user [:user]', [
                        'username' => $authUser->name,
                        'role' => $role->name,
                        'user' => $user->name,
                    ]));

                self::$notify = true;
            }
        }
    }

    public static function revokePermissions(User $user, array $permissionDescriptions): void
    {
        $authUser = auth()->user();

        foreach ($user->permissions as $permission)
        {
            if (!in_array($permission->description, $permissionDescriptions, true))
            {
                $user->revokePermissionTo($permission);

                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('deleted')
                    ->withProperties([
                        __('revoked_permission') => $permission,
                        __('to_user') => $user,
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
                    ->log(__(':username: revoked the [:permission] permission from the [:user] user', [
                        'username' => $authUser->name,
                        'permission' => $permission->description,
                        'user' => $user->name,
                    ]));

                self::$notify = true;
            }
        }
    }

    private static function givePermissions(User $user, array $permissionDescriptions): void
    {
        $authUser = auth()->user();
        $user->givePermissionTo($permissionDescriptions);

        foreach ($permissionDescriptions as $permissionDescription)
        {
            $permission = Permission::where('description', $permissionDescription)->first();

            if (!$user->hasPermissionTo($permission->id))
            {
                $user->givePermissionTo($permission);

                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('created')
                    ->withProperties([
                        __('granted_permission') => $permission,
                        __('to_user') => $user,
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
                    ->log(__(':username: granted the [:permission] permission to user [:user]', [
                        'username' => $authUser->name,
                        'permission' => $permission->description,
                        'user' => $user->name,
                    ]));

                self::$notify = true;
            }
        }
    }

    private static function setPerson(User $user, array $inputs): void
    {
        $authUser = auth()->user();

        if ($inputs['is_external'])
        {
            // el usuario pasó de ser interno (corporativo) a externo (persona ajena a la organización)
            // por lo tanto se eliminan las asociaciones activas entre el usuario y las
            // unidades administrativas de la organización.
            foreach ($user->organizationalUnits as $ou)
            {
                $user->organizationalUnits()->detach($ou->id);
                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('deleted')
                    ->withProperties([
                        __('disassociated_user') => $user,
                        __('from_administrative_unit') => $ou,
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
                    ->log(__(':username: disassociated user [:user] from the administrative unit [:ou]', [
                        'username' => $authUser->name,
                        'user' => $user->name,
                        'ou' => $ou->name,
                    ]));

                self::$notify = true;
            }
        }
        else
        {
            // el usuario sigue siendo corporativo pero es movido a otra unidad
            // administrativa (ua) dentro de la organización, se deben desactivar las
            // uas activas.
            foreach ($user->activeOrganizationalUnits as $ou)
            {
                if (!in_array($ou->name, $inputs['ou_names'], true))
                {
                    $user->activeOrganizationalUnits()->updateExistingPivot($ou->id, [
                        'disabled_at' => now()->toIso8601String(),
                    ]);

                    activity()
                        ->causedBy($authUser)
                        ->performedOn($user)
                        ->event('updated')
                        ->withProperties([
                            __('deactivated_user') => $user,
                            __('in_administrative_unit') => $ou,
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
                        ->log(__(':username: deactivated user [:user] in the administrative unit [:ou]', [
                            'username' => $authUser->name,
                            'user' => $user->name,
                            'ou' => $ou->name,
                        ]));

                    self::$notify = true;
                }
            }
            // y ahora se deben registrar las nuevas asociaciones del usuario
            // a las nuevas unidades administrativas o, si ya existían, reactivarlas.
            foreach ($inputs['ou_names'] as $ouName)
            {
                $ou = OrganizationalUnit::where('name', $ouName)->first();

                if (!in_array($ouName, $user->organizationalUnits->pluck('name')->all(), true))
                {
                    $user->organizationalUnits()->attach($ou->id);
                    activity()
                        ->causedBy($authUser)
                        ->performedOn($user)
                        ->event('created')
                        ->withProperties([
                            __('associated_user') => $user,
                            __('with_administrative_unit') => $ou,
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
                        ->log(__(':username: associated user [:user] with the administrative unit [:ou]', [
                            'username' => $authUser->name,
                            'user' => $user->name,
                            'ou' => $ou->name,
                        ]));

                    self::$notify = true;
                }
                else
                {
                    $user->organizationalUnits()->updateExistingPivot($ou->id, ['disabled_at' => null]);
                    activity()
                        ->causedBy($authUser)
                        ->performedOn($user)
                        ->event('updated')
                        ->withProperties([
                            __('activated_user') => $user,
                            __('in_administrative_unit') => $ou,
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
                        ->log(__(':username: activated user [:user] in the administrative unit [:ou]', [
                            'username' => $authUser->name,
                            'user' => $user->name,
                            'ou' => $ou->name,
                        ]));

                    self::$notify = true;
                }
            }
        }

        if (array_key_exists('id_card', $inputs) && empty($inputs['id_card']))
        {
            $user->person()->delete();
            self::$notify = true;
        }
        elseif (isset($inputs["id_card"]) && isset($inputs["names"]) && isset($inputs["surnames"]))
        {
            $person = $user->person ?? new Person();
            $person->id_card = $inputs['id_card'];
            $person->names = $inputs['names'];
            $person->surnames = $inputs['surnames'];
            $person->position = $inputs['position'] ?? null;
            $person->staff_type = $inputs['staff_type'] ?? null;

            $user->person ?: $person->user()->associate($user);

            if ($person->isDirty())
            {
                self::$notify = true;
            }

            $person->save();
        }
    }
}
