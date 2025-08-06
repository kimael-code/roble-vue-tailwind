<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Security\Permission\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export permissions'))
        {
            abort(403);
        }

        return new ExportIndexToPdf(filters: $request->all())->make();
    }
}
