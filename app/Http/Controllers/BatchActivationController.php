<?php

namespace App\Http\Controllers;

use App\Actions\Security\User\BatchActivateUser;
use Illuminate\Http\Request;

class BatchActivationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $resource)
    {
        switch ($resource)
        {
            case 'users':
                $result = BatchActivateUser::execute($request->all());
                return redirect(route('users.index'))->with('message', $result);

            default:
                # code...
                break;
        }
    }
}
