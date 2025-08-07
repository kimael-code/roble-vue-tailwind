<?php

namespace App\Actions\Security;

use App\Models\User;

class BatchDeleteUser
{
    public static function execute(array $ids): array
    {
        $msg = [
            'message' => 'registros eliminados.',
            'title' => __('PROCESSED!'),
            'type' => 'success',
        ];
        $deleteCount = 0;
        $nonDeleteCount = 0;
        $nonDeleteReasons = '';

        foreach ($ids as $id => $isSelected)
        {
            if (!$isSelected)
            {
                continue;
            }

            $sysAdminRole = __('Systems Administrator');
            $sysadminsCount = User::withTrashed()->with('roles')->get()->filter(
                fn($user) => $user->roles->where('name', $sysAdminRole)->toArray()
            )->count();

            $superuserRole = __('Superuser');
            $superusersCount = User::withTrashed()->with('roles')->get()->filter(
                fn($user) => $user->roles->where('name', $superuserRole)->toArray()
            )->count();

            $user = User::find($id);

            if ($user->is(auth()->user()))
            {
                $nonDeleteCount += 1;
                $nonDeleteReasons .= 'usted no puede eliminar su propia cuenta';
                $nonDeleteReasons .= $nonDeleteCount > 1 ? ', ' : '';
            }
            elseif ($sysadminsCount === 1 && $superusersCount === 0 && $user->hasRole($sysAdminRole))
            {
                $nonDeleteCount += 1;
                $nonDeleteReasons .= 'única cuenta de administrador de sistemas';
                $nonDeleteReasons .= $nonDeleteCount > 1 ? ', ' : '';
            }
            elseif ($sysadminsCount === 0 && $superusersCount === 1 && $user->hasRole($sysAdminRole))
            {
                $nonDeleteCount += 1;
                $nonDeleteReasons .= 'única cuenta de administrador de sistemas';
                $nonDeleteReasons .= $nonDeleteCount > 1 ? ', ' : '';
            }
            else
            {
                $user->delete();
                $deleteCount += 1;
            }
        }

        if ($deleteCount === 1)
        {
            $msg['message'] = "$deleteCount registro eliminado";
            $msg['type'] = 'success';
        }
        else
        {
            $msg['message'] = "$deleteCount registros eliminados";
            $msg['type'] = 'success';
        }

        if ($nonDeleteCount === 1)
        {
            $msg['message'] .= ". $nonDeleteCount registro NO eliminado. Causa: $nonDeleteReasons";
            $msg['type'] = 'warning';
        }
        elseif ($nonDeleteCount > 1)
        {
            $msg['message'] .= ". $nonDeleteCount registros NO eliminados. Causa: $nonDeleteReasons";
            $msg['type'] = 'warning';
        }

        $msg['message'] .= '.';

        return $msg;
    }
}
