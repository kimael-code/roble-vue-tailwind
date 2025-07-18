<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Debugging\ActivityLog\ExportIndexToDomPdf;
use App\Actions\Debugging\ActivityLog\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityLogReportController extends Controller
{
    public function indexToPdf(Request $request): Response
    {
        // $pdf = new ExportIndexToPdf(filters: $request->all());
        // $pdf = new ExportIndexToDomPdf($request->all());

        return ExportIndexToDomPdf::make($request->all())
            ->stream('document.pdf');
    }
}
