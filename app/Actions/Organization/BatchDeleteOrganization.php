<?php

namespace App\Actions\Organization;

use App\Models\Organization\Organization;

class BatchDeleteOrganization
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

            $organization = Organization::find($id);

            if (!$organization->disabled_at || $organization->organizationalUnits()->exists())
            {
                $nonDeleteCount += 1;
            }
            else
            {
                $organization->delete();
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
            $msg['message'] .= ". $nonDeleteCount registro NO eliminado. Causa/s: asociación de registros";
            $msg['type'] = 'warning';
        }
        elseif ($nonDeleteCount > 1)
        {
            $msg['message'] .= ". $nonDeleteCount registros NO eliminados. Causa/s: asociación de registros";
            $msg['type'] = 'warning';
        }

        $msg['message'] .= '.';

        return $msg;
    }
}
