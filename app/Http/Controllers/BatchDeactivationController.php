<?php

namespace App\Http\Controllers;

use App\Actions\Security\BatchDeactivateUser;
use Illuminate\Http\Request;

class BatchDeactivationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $resource)
    {
        switch ($resource)
        {
            case 'users':
                $result = BatchDeactivateUser::execute($request->all());
                return redirect(route('users.index'))->with('message', $result);

            default:
                # code...
                break;
        }
    }
}
