<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;

class CreatePermission
{
    public static function handle(array $inputs): Permission
    {
        return Permission::create($inputs);
    }
}
