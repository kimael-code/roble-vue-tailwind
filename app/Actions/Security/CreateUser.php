<?php

namespace App\Actions\Security;

use App\Models\Organization\OrganizationalUnit;
use App\Models\Person;
use App\Models\Security\Permission;
use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser
{
    public static function handle(array $inputs): User
    {
        $user = new User();

        DB::transaction(function () use ($inputs, &$user)
        {
            $user->name = $inputs['name'];
            $user->email = $inputs['email'];
            $user->password = $inputs['id_card'] ?: Hash::make('12345678');
            $user->remember_token = Str::random();
            $user->is_external = $inputs['is_external'];
            $user->save();

            self::assignRoles($user, $inputs['roles']);
            self::givePermissions($user, $inputs['permissions']);
            self::setPerson($user, $inputs);
            self::setOrganizationalUnits($user, $inputs);
        });

        return $user;
    }

    private static function assignRoles(User $user, array $roleNames): void
    {
        $user->assignRole($roleNames);

        foreach ($roleNames as $roleName)
        {
            $role = Role::findByName($roleName);
            $authUser = auth()->user();

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
                ->log(__(':username: assigned the role [:rolename] to user [:user]', [
                    'username' => $authUser,
                    'rolename' => $role->name,
                    'user' => $user->name,
                ]));
        }
    }

    private static function givePermissions(User $user, array $permissionNames): void
    {
        $user->givePermissionTo($permissionNames);

        foreach ($permissionNames as $permissionID)
        {
            $permission = Permission::find($permissionID);
            $authUser = auth()->user();

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
                ->log(__(':username: granted the [:permission] permission to user [:user]', [
                    'username' => $authUser,
                    'permission' => $permission->description,
                    'user' => $user->name,
                ]));
        }
    }

    private static function setPerson(User $user, array $inputs): void
    {
        if ($inputs['id_card'] && $inputs['names'] && $inputs['surnames'])
        {
            $person = new Person();
            $person->id_card = $inputs['id_card'];
            $person->names = $inputs['names'];
            $person->surnames = $inputs['surnames'];
            $person->position = $inputs['position'];
            $person->staff_type = $inputs['staff_type'];

            $person->user()->associate($user);
            $person->save();
        }
    }

    private static function setOrganizationalUnits(User $user, array $inputs): void
    {
        if ($inputs['ou_names'])
        {
            foreach ($inputs['ou_names'] as $ouName)
            {
                $ou = OrganizationalUnit::where(DB::raw('LOWER(name)'), '=', DB::raw("LOWER('" . $ouName . "')"))->first();
                if (!$ou)
                    $ou = OrganizationalUnit::where('name', 'NO DEFINIDA')->first();

                $user->organizationalUnits()->attach($ou);

                $authUser = auth()->user();

                activity()
                    ->causedBy($authUser)
                    ->performedOn($user)
                    ->event('created')
                    ->withProperties([
                        'ou' => $ou,
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
                        'username' => $authUser,
                        'user' => $user->name,
                        'ou' => $ou->name,
                    ]));
            }
        }
    }
}
