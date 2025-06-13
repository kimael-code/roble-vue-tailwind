<?php

namespace App\Http\Controllers;

use App\Actions\Security\BatchDeletePermission;
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
                return redirect(route('permissions.index'))->with('msg', $result);

            default:
                # code...
                break;
        }
    }
}
