<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;

class BatchDeletePermission
{
    public static function execute(array $ids): array
    {
        $msg = [
            'msg' => 'registros eliminados.',
            'title' => 'PROCESADO',
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

            if ($permission->users()->count() > 0 || $permission->roles()->count() > 0)
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
            $msg['msg'] = "$deleteCount registro eliminado";
            $msg['type'] = 'success';
        }
        else
        {
            $msg['msg'] = "$deleteCount registros eliminados";
            $msg['type'] = 'success';
        }

        if ($nonDeleteCount === 1)
        {
            $msg['msg'] .= ". $nonDeleteCount registro NO eliminado. Causa: asociado a otros registros";
            $msg['type'] = 'warning';
        }
        elseif ($nonDeleteCount > 1)
        {
            $msg['msg'] .= ". $nonDeleteCount registros NO eliminados. Causa: asociados a otros registros";
            $msg['type'] = 'warning';
        }

        $msg['msg'] .= '.';

        return $msg;
    }
}
