<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Debugging\ActivityLog\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        $pdf = new ExportIndexToPdf(filters: $request->all());

        return $pdf->make();
    }
}
