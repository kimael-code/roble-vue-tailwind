<?php

namespace App\Http\Controllers;

use App\Actions\Debugging\ActivityLog\ExportActivityLogIndex;
use Illuminate\Http\Request;

class ExportDataController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $resource)
    {
        match ($resource) {
            'activity-logs' => (new ExportActivityLogIndex)->handle($request->all()),
            //  => ,
        };
    }
}
