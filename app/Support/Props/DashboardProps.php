<?php

namespace App\Support\Props;

use App\Models\Security\Role;
use App\Models\User;
use App\Support\Logs\Logfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardProps
{
    public static function all(User $user): array
    {
        $props = ['can' => self::getPermissions($user),];

        if ($user->can('read sysadmin dashboard'))
        {
            $props['sysadminData']['users'] = [
                'count' => User::withTrashed()->count(),
                'series' => [User::count(), User::onlyTrashed()->count(),],
                'labels' => ['Activos', 'Eliminados'],
            ];
            $props['sysadminData']['roles'] = [
                'count' => Role::count(),
                'series' => array_values(self::usersCountHavingRole()),
                'labels' => array_keys(self::usersCountHavingRole()),
            ];
            $props['sysadminData']['activeUsers'] = collect(
                DB::table('sessions')
                    ->selectRaw('distinct on (user_id) user_id, ip_address, user_agent, last_activity')
                    ->whereNotNull('user_id')
                    ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
                    ->orderBy('user_id')
                    ->orderBy('last_activity', 'desc')
                    ->get()
            )->map(function ($session)
            {
                return (object) [
                    'user' => User::find($session->user_id),
                    'ip_address' => $session->ip_address,
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
            $props['sysadminData']['logSizes'] = (new Logfile())->logSizes();
        }

        return $props;
    }

    private static function getPermissions(User $user): array
    {
        return [
            'dashboardSysadmin' => $user->can('read sysadmin dashboard') || $user->hasRole(['Súper Administrador']),
        ];
    }

    private static function usersCountHavingRole(): array
    {
        $result = [];

        foreach (Role::all() as $role)
        {
            $result[$role->name] = User::with('roles')
                ->get()
                ->filter(fn($user) => $user->roles->where('name', $role->name)->toArray())
                ->count();
        }

        return $result;
    }
}
