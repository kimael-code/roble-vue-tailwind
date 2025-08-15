<?php

namespace App\Actions\Security;

use App\Models\Security\Role;

class BatchDeleteRole
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

            $role = Role::find($id);

            if ($role->id === 1)
            {
                $nonDeleteCount += 1;
                $nonDeleteReasons .= $nonDeleteCount > 1 ? ', ' : '';
                $nonDeleteReasons .= 'Superusuario no es eliminable';
            }
            elseif ($role->permissions()->exists() || $role->users()->exists())
            {
                $nonDeleteCount += 1;
                $nonDeleteReasons .= $nonDeleteCount > 1 ? ', ' : '';
                $nonDeleteReasons .= 'asociación de registros';
            }
            else
            {
                $role->delete();
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
            $msg['message'] .= ". $nonDeleteCount registro NO eliminado. Causa/s: $nonDeleteReasons";
            $msg['type'] = 'warning';
        }
        elseif ($nonDeleteCount > 1)
        {
            $msg['message'] .= ". $nonDeleteCount registros NO eliminados. Causa/s: $nonDeleteReasons";
            $msg['type'] = 'warning';
        }

        $msg['message'] .= '.';

        return $msg;
    }
}
