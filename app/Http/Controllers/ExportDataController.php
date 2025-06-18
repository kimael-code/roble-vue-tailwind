<?php

namespace App\Http\Controllers;

use App\Actions\Debugging\ActivityLog\ExportActivityLogIndex;
use Illuminate\Http\Request;

class ExportDataController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $resource, ?string $format = 'pdf')
    {
        // dd($resource);
        return match ("$resource/$format") {
            'activity-logs/pdf' => (new ExportActivityLogIndex)->handle($request->all()),
            //  => ,
        };
    }
}
