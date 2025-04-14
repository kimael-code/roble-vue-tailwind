<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;

class UpdatePermission
{
    public static function handle(array $inputs, Permission $permission): void
    {
        $permission->update($inputs);
    }
}
