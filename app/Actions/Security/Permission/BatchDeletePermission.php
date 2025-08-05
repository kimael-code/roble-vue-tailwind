<?php

namespace App\Actions\Security\Permission;

use App\Models\Security\Permission;

class BatchDeletePermission
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

        foreach ($ids as $id => $isSelected)
        {
            if (!$isSelected)
            {
                continue;
            }

            $permission = Permission::find($id);

            if ($permission->users()->exists() || $permission->roles()->exists())
            {
                $nonDeleteCount += 1;
            }
            else
            {
                $permission->delete();
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
            $msg['message'] .= ". $nonDeleteCount registro NO eliminado. Causa: asociado a otros registros";
            $msg['type'] = 'warning';
        }
        elseif ($nonDeleteCount > 1)
        {
            $msg['message'] .= ". $nonDeleteCount registros NO eliminados. Causa: asociados a otros registros";
            $msg['type'] = 'warning';
        }

        $msg['message'] .= '.';

        return $msg;
    }
}
