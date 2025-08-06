<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Security\User\ExportIndexToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export users'))
        {
            abort(403);
        }

        return new ExportIndexToPdf(filters: $request->all())->make();
    }
}
