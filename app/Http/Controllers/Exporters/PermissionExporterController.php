<?php

namespace App\Http\Controllers\Exporters;

use App\Actions\Security\ExportPermissionsToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionExporterController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export permissions'))
        {
            abort(403);
        }

        return new ExportPermissionsToPdf(filters: $request->all())->make();
    }
}
