<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Debugging\ActivityLog\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export activity traces'))
        {
            abort(403);
        }

        return new ExportIndexToPdf(filters: $request->all())->make();
    }
}
