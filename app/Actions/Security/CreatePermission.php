<?php

namespace App\Actions\Security;

use App\Models\Security\Permission;

class CreatePermission
{
    public function handle(array $inputs): Permission
    {
        return Permission::create($inputs);
    }
}
