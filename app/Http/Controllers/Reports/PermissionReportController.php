<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Security\Permission\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        $pdf = new ExportIndexToPdf(filters: $request->all());

        return $pdf->make();
    }
}
