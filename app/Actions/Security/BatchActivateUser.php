<?php

namespace App\Actions\Security;

use App\Models\User;

class BatchActivateUser
{
    public static function execute(array $ids): array
    {
        $msg = [
            'message' => 'registros activados.',
            'title' => __('PROCESSED!'),
            'type' => 'success',
        ];
        $activateCount = 0;
        $nonActivateCount = 0;
        $nonActivateReasons = '';

        foreach ($ids as $id => $isSelected)
        {
            if (!$isSelected)
            {
                continue;
            }

            $user = User::find($id);

            if ($user->is(auth()->user()))
            {
                $nonActivateCount += 1;
                $nonActivateReasons .= $nonActivateCount > 1 ? ', ' : '';
                $nonActivateReasons .= 'usted no puede activar su propia cuenta';
            }
            elseif (!$user->disabled_at && !$user->deleted_at)
            {
                $nonActivateCount += 1;
                $nonActivateReasons .= $nonActivateCount > 1 ? ', ' : '';
                $nonActivateReasons .= "usuario/a {$user->name} ya está activado/a";
            }
            else
            {
                $user->disabled_at = null;
                $user->deleted_at = null;
                $user->save();
                $activateCount += 1;
            }
        }

        if ($activateCount === 1)
        {
            $msg['message'] = "$activateCount registro activado";
            $msg['type'] = 'success';
        }
        else
        {
            $msg['message'] = "$activateCount registros activados";
            $msg['type'] = 'success';
        }

        if ($nonActivateCount === 1)
        {
            $msg['message'] .= ". $nonActivateCount registro NO activado. Causa: $nonActivateReasons";
            $msg['type'] = 'warning';
        }
        elseif ($nonActivateCount > 1)
        {
            $msg['message'] .= ". $nonActivateCount registros NO activados. Causa/s: $nonActivateReasons";
            $msg['type'] = 'warning';
        }

        $msg['message'] .= '.';

        return $msg;
    }
}
