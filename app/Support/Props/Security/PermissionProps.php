<?php

namespace App\Support\Props\Security;

use App\Http\Resources\Security\PermissionCollection;
use App\Models\Security\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PermissionProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => Auth::user()->can('create permissions'),
            'read' => Auth::user()->can('read permission'),
            'update' => Auth::user()->can('update permissions'),
            'delete' => Auth::user()->can('delete permissions'),
            'export' => Auth::user()->can('export permissions'),
        ];
    }

    public static function index(): array
    {
        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'name', 'description']),
            'permissions' => fn() => new PermissionCollection(
                Permission::filter(Request::only(['search', 'name', 'description']))
                    ->paginate(10)
                    ->withQueryString()
            ),
        ];
    }
}

