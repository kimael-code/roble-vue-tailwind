<?php

namespace App\Http\Controllers\Exporters;

use App\Actions\Debugging\ExportActivityLogsToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogExporterController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export activity traces'))
        {
            abort(403);
        }

        return new ExportActivityLogsToPdf(filters: $request->all())->make();
    }
}
