<?php

namespace App\Http\Controllers;

use App\Actions\Organization\BatchDeleteOrganization;
use App\Actions\Organization\BatchDeleteOrganizationalUnit;
use App\Actions\Security\BatchDeletePermission;
use App\Actions\Security\BatchDeleteRole;
use App\Actions\Security\BatchDeleteUser;
use Illuminate\Http\Request;

class BatchDeletionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $resource)
    {
        switch ($resource)
        {
            case 'permissions':
                $result = BatchDeletePermission::execute($request->all());
                return redirect(route('permissions.index'))->with('message', $result);
            case 'organizations':
                $result = BatchDeleteOrganization::execute($request->all());
                return redirect(route('organizations.index'))->with('message', $result);
            case 'organizational-units':
                $result = BatchDeleteOrganizationalUnit::execute($request->all());
                return redirect(route('organizational-units.index'))->with('message', $result);
            case 'roles':
                $result = BatchDeleteRole::execute($request->all());
                return redirect(route('roles.index'))->with('message', $result);
            case 'users':
                $result = BatchDeleteUser::execute($request->all());
                return redirect(route('users.index'))->with('message', $result);

            default:
                # code...
                break;
        }
    }
}
