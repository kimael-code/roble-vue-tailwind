<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Security\Role\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export roles'))
        {
            abort(403);
        }

        return new ExportIndexToPdf(filters: $request->all())->make();
    }
}
