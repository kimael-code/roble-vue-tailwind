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
    protected static $flashNotify = false;

    public static function handle(User $user, array $inputs): User
    {
        DB::transaction(function () use ($inputs, &$user)
        {
            $user->name = $inputs['name'];
            $user->email = $inputs['email'];
            $user->is_external = $inputs['is_external'];
            $user->save();

            self::removeRoles($user, $inputs['roles']);
            self::assignRoles($user, $inputs['roles']);
            self::revokePermissions($user, $inputs['permissions']);
            self::givePermissions($user, $inputs['permissions']);
            self::setPerson($user, $inputs);

            if (self::$flashNotify)
            {
                session()->flash('msg', [
                    'msg' => "{$user->name}",
                    'title' => __('SAVED!'),
                    'type' => 'success',
                ]);
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
                        'role' => $role,
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__("{$role->name} role unassigned to the user {$user->name}"));

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new ActionHandledOnModel(
                //         $authUser,
                //         [
                //             'id' => $role->id,
                //             'name' => $role->name,
                //             'timestamp' => now()->toISOString(),
                //         ],
                //         'unasigned',
                //         ['routeName' => 'users', 'routeParam' => 'user']
                //     ));
                // }
                self::$flashNotify = true;
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
                        'role' => $role,
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__('role assigned to user'));

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new ActionHandledOnModel(
                //         $authUser,
                //         [
                //             'id' => $role->id,
                //             'name' => $role->name,
                //             'timestamp' => now()->toISOString(),
                //         ],
                //         'assigned',
                //         ['routeName' => 'users', 'routeParam' => 'user']
                //     ));
                // }
                self::$flashNotify = true;
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
                        'permission' => $permission,
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__('permission revoked to user'));

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new ActionHandledOnModel(
                //         $authUser,
                //         [
                //             'id' => $permission->id,
                //             'name' => $permission->description,
                //             'timestamp' => now()->toISOString(),
                //         ],
                //         'unassigned',
                //         ['routeName' => 'users', 'routeParam' => 'user']
                //     ));
                // }
                self::$flashNotify = true;
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
                        'permission' => $permission,
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__('permission given to user'));

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new ActionHandledOnModel(
                //         $authUser,
                //         [
                //             'id' => $permission->id,
                //             'name' => $permission->description,
                //             'timestamp' => now()->toISOString(),
                //         ],
                //         'assigned',
                //         ['routeName' => 'users', 'routeParam' => 'user']
                //     ));
                // }
                self::$flashNotify = true;
            }
        }
    }

    private static function setPerson(User $user, array $inputs): void
    {
        // dd($user, $inputs);
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
                        'organizationalUnit' => $ou,
                        'request' => [
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->header('user-agent'),
                            'user_agent_lang' => request()->header('accept-language'),
                            'referer' => request()->header('referer'),
                            'http_method' => request()->method(),
                            'request_url' => request()->fullUrl(),
                        ]
                    ])
                    ->log(__('user detached from organizational unit'));

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new OrganizationalUnitDetachedFromUser($ou, now()->toISOString(), $user, $authUser));
                // }
                self::$flashNotify = true;
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
                        'disabled_at' => now()->toISOString(),
                    ]);

                    activity()
                        ->causedBy($authUser)
                        ->performedOn($user)
                        ->event('updated')
                        ->withProperties([
                            'organizationalUnit' => $ou,
                            'request' => [
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->header('user-agent'),
                                'user_agent_lang' => request()->header('accept-language'),
                                'referer' => request()->header('referer'),
                                'http_method' => request()->method(),
                                'request_url' => request()->fullUrl(),
                            ]
                        ])
                        ->log(__('user disabled on organizational unit'));

                    // $notifiableUsers = User::permission('update users')->get()->filter(
                    //     fn(User $user) => $user->id != $authUser->id
                    // )->all();

                    // foreach ($notifiableUsers as $notiUser)
                    // {
                    //     $notiUser->notify(new OrganizationalUnitDisabledOnUser($ou, now()->toISOString(), $user, $authUser));
                    // }
                    self::$flashNotify = true;
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
                            'organizationalUnit' => $ou,
                            'request' => [
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->header('user-agent'),
                                'user_agent_lang' => request()->header('accept-language'),
                                'referer' => request()->header('referer'),
                                'http_method' => request()->method(),
                                'request_url' => request()->fullUrl(),
                            ]
                        ])
                        ->log(__('user attached to organizational unit'));

                    // $notifiableUsers = User::permission('update users')->get()->filter(
                    //     fn(User $user) => $user->id != $authUser->id
                    // )->all();

                    // foreach ($notifiableUsers as $notiUser)
                    // {
                    //     $notiUser->notify(new OrganizationalUnitAttachedToUser($ou, now()->toISOString(), $user, $authUser));
                    // }
                    self::$flashNotify = true;
                }
                else
                {
                    $user->organizationalUnits()->updateExistingPivot($ou->id, ['disabled_at' => null]);
                    activity()
                        ->causedBy($authUser)
                        ->performedOn($user)
                        ->event('updated')
                        ->withProperties([
                            'organizationalUnit' => $ou,
                            'request' => [
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->header('user-agent'),
                                'user_agent_lang' => request()->header('accept-language'),
                                'referer' => request()->header('referer'),
                                'http_method' => request()->method(),
                                'request_url' => request()->fullUrl(),
                            ]
                        ])
                        ->log(__('user enabled on organizational unit'));

                    // $notifiableUsers = User::permission('update users')->get()->filter(
                    //     fn(User $user) => $user->id != $authUser->id
                    // )->all();

                    // foreach ($notifiableUsers as $notiUser)
                    // {
                    //     $notiUser->notify(new OrganizationalUnitAttachedToUser($ou, now()->toISOString(), $user, $authUser));
                    // }
                    self::$flashNotify = true;
                }
            }
        }

        if (($inputs['id_card'] && $inputs['names']))
        {
            $person = $user->person ?? new Person();
            $person->id_card = $inputs['id_card'];
            $person->names = $inputs['names'];
            $person->surnames = $inputs['surnames'];
            $person->position = $inputs['position'];
            $person->staff_type = $inputs['staff_type'];

            $user->person ?: $person->user()->associate($user);

            if ($person->isDirty())
            {
                self::$flashNotify = true;

                // $notifiableUsers = User::permission('update users')->get()->filter(
                //     fn(User $user) => $user->id != $authUser->id
                // )->all();

                // foreach ($notifiableUsers as $notiUser)
                // {
                //     $notiUser->notify(new UserUpdated($user, $authUser));
                // }
            }

            $person->save();
        }
    }
}
