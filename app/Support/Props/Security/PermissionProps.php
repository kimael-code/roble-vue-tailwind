<?php

namespace App\Support\Props\Security;

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
            'columns' => [
                [
                    'name' => 'name',
                    'value' => __('model.field.name'),
                    'way' => '',
                ],
                [
                    'name' => 'description',
                    'value' => __('model.field.description'),
                    'way' => '',
                ],
            ],
            'filters' => Request::all(['search', 'name', 'description',]),
            'permissions' => fn() => Permission::filter(Request::only(['search', 'name', 'description',]))
                    ->paginate()
                    ->withQueryString(),
        ];
    }
}

